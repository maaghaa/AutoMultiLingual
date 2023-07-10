<?php
// This class lets you automatically translate your project to other languages using Google Translate
class AutoMultiLingual {
  // The default language
  private $default_lang = 'en';

  // The selected language
  public $selected_lang;

  // The database connection
  private $db;

  // The constructor
  public function __construct($lang) {
    // Set the selected language
    $this->selected_lang = $lang;

    // Connect to the database
    $this->db = new mysqli('localhost', 'username', 'password', 'database');

    // Check for errors
    if ($this->db->connect_error) {
      die('Connection failed: ' . $this->db->connect_error);
    }
  }

  // The function that translates a string
  public function Lang($string, ...$args) {

    // Generate the md5 hash of the string
    $hash = md5(strtolower(trim($string)));

    // Check if the hash exists in the database
    $result = $this->db->query("SELECT * FROM translation WHERE hash = '$hash'");

    // If the hash exists, return the translated string or the default string
    if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      if ($row[$this->selected_lang]) {
        $rt=$row[$this->selected_lang];
      } else {
        $rt=$string;
      }
    } else {
      // If the hash does not exist, insert a new row with the default string
      $this->db->query("INSERT INTO translation (hash, timestamp, en) VALUES ('$hash', time(), '$string')");

      // Return the default string
      $rt=$string;
    }

    //Replace WW with args one by one in order
    for($a=1;$a<count($args);$a++){
      $rt=preg_replace('/WW/',trim($args[$a]),$rt,1);
    }

    return $rt;
  }
}
