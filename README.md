# AutoMultiLingual
This is a php function that lets you automatically translate your project to other languages automatically using google translate.

# How to use
after setting up the prerequisites, you just pass the string that you want to be translated to the `Lang()` function. it automatically checks the selected language, replaces the English string you entered with the target language string and thats all.

**The class version:**
The class version has recently been created. it how you use it:
```
//Create a new instance of the class and choose Arabic as target language
$mlang=new AutoMultiLingual('ar');

//Translate the string and echo it
echo $mlang->Lang("Hello dear WW!",$user_nickname);

//To change the language to korean
$mlang->selected_lang='kr';
```

# How it works
Every String that you use gets converted to an md5 hash, the hash will get checked in the database, if it exists, done, if not, it will be added to the database as a new row. every language is a column, a cron job runs every miniute and checks what string have not yet been translated, grabs the English string, queries google translate and saves the translated string to the related column.

**Please note that if the string is not yet translated, the same (English) string will get returned.**

# A very simple example
  ```
  $lang='fr';
  $the_french_string=Lang("This is only a Test");
  echo $the_french_string;

  $lang='kr':
  $the_korean_string=Lang("This is only a Test");
  echo $the_korean_string;
  ```

# The MySQl Table
```
CREATE TABLE `translation` (
 `hash` varchar(40) NOT NULL,
 `timestamp` int(10) NOT NULL,
 `en` text NOT NULL,
 `ru` text DEFAULT NULL,
 `ar` text DEFAULT NULL,
 `tr` text DEFAULT NULL,
 PRIMARY KEY (`hash`),
 KEY `timestamp` (`timestamp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

# How to add new languages?
Well, just add a new column to the table with the two letters name of that language. Make sure to use the exact name that google translate uses. (After choosing the source and the target language in translate.google.com, you can find it in the url).

For example if you want to add the Dutch language you can execute this query:
```
ALTER TABLE `translation` ADD `nl` TEXT NULL DEFAULT NULL;
```

# Can I also have non-translatable words in the string?
Yes, of course! You easily can put a `WW` in the string. Google doesn't translate it! then you can pass the replace word as extra args to the function in order.
For example:
```
$lang='ar';
$translated=Lang("Hello Dear WW!",$user_nickname);
```

# Why did we use a cronjob and we didn't do the translation inline
Well, it's obvious. you might have added many new strings to your project in the last commit. and you might have your project done in 20 languages. if we query google for this many languages at once, your server IP might get banned. So we preferred to do the translation one by one, So don't worry. continue developing, the cronjob will start translating sentences one by one in the background. Go have your cup of coffee! You'r translateions will be ready in a while!
