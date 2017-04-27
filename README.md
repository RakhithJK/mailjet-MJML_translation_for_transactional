# Building a multi brands and multi languages transactional template generation solution 

This POC will show how to leverage MJML to simply manage advanced transactional template with multiple brands and multiple languages. 

![](Flow.png)

This sample leverage (MJML)[https://mjml.io], the only framework that makes responsive email easy, to ease the management of reusable piece of responsive templates.

The step covered by this sample are as follow : 

 - Processing of MJML to get a HTML email
 - Substitution of translation placeholders with a database of transalation (to make it easier here we are using PHP files to store the different languages)
 - Creation or update of template through Mailjet API 

This project contain several folders : 

 - [input](https://github.com/eboisgon/MJML_translation_for_transactional/tree/master/input) : all the MJML files used to represent the final messages
 - [output](https://github.com/eboisgon/MJML_translation_for_transactional/tree/master/output) : result of MJML processing (still contain the translation placeholders)
 - [output_trans](https://github.com/eboisgon/MJML_translation_for_transactional/tree/master/output_trans) : contain the final templates that will be stored on Mailjet
 - [trans](https://github.com/eboisgon/MJML_translation_for_transactional/tree/master/trans) : contain the translation indexes, one file per language. 

## Use of MJML 

To mutualise some part of the template (header, footer, snippets), we are leveraging here the use of the `<MJ-INCLUDE>` MJML tag. It allows to load external files to build you email template. (see (here)[https://mjml.io/documentation/#mj-include] for more information) 

As a convention all headers, footers and snippets name start with a `_` and will not be directly processed by the script. 

## Install and Run 

To install [Mailjet PHP wrapper](https://github.com/mailjet/mailjet-apiv3-php) : 

`composer install`

To install the MJML CLI :

`npm install mjml`

More details [here](https://mjml.io/documentation/#installation) 

Configuration in conf.php 

 - set your Mailjet API keys 
 - set your the command line for the MJML CLI in `$path_mjml`

Next just need to run 

`php process.php`


