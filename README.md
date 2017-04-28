# Building a multi brands and multi languages transactional template generation solution 

This POC will show how to leverage MJML to simply manage advanced transactional template with multiple brands and multiple languages. 

![](Flow.png)

This sample leverage (MJML)[https://mjml.io], the only framework that makes responsive email easy, to ease the management of reusable piece of responsive templates.

The step covered by this sample are as follow : 

 - Processing of MJML to get a HTML email
 - Substitution of translation placeholders with a database of transalation (to make it easier here we are using PHP files to store the different languages)
 - Creation or update of template through Mailjet API 

The MJML templates will also be able to accommodate Mailjet Template Language as described [here](https://dev.mailjet.com/template-language/mjml/).

This project contain several folders : 

 - [input](https://github.com/eboisgon/MJML_translation_for_transactional/tree/master/input) : all the MJML files used to represent the final messages
 - [output](https://github.com/eboisgon/MJML_translation_for_transactional/tree/master/output) : result of MJML processing (still contain the translation placeholders)
 - [output_trans](https://github.com/eboisgon/MJML_translation_for_transactional/tree/master/output_trans) : contain the final templates that will be stored on Mailjet
 - [trans](https://github.com/eboisgon/MJML_translation_for_transactional/tree/master/trans) : contain the translation indexes, one file per language. 

## Use of MJML 

To mutualise some part of the template (header, footer, snippets), we are leveraging here the use of the `<MJ-INCLUDE>` MJML tag. It allows to load external files to build you email template. (see (here)[https://mjml.io/documentation/#mj-include] for more information) 

As a convention all headers, footers and snippets name start with a `_` and will not be directly processed by the script. 

## Translation 

This example relies on text based transalation sources (in /trans folder) but could be modified to use a database storage. 
To avoid any conflict between the Mailjet ttemplating language (based on Jinja syntax) and the translation placeholders, the substitution tags for the translation are using [[ ]]. 

## Storing the final product on Mailjet

The script in its last stage will store the HTML after transaltion on a Mailjet account. 

You can find more details about the Mailjet Template API [here](https://dev.mailjet.com/guides/#template-api).

The storage and maintenance of the template is relying on a naming convention of the template inherited from the original file in the /input folder. 

/input/Filename.mjml => /output/Filename.html => /output_trans/Filename_$lg.html => Filename_$lg templates

## Mailjet Transactional calls 

By using the name of the template in the Send API call, it will be possible to directly send a message with the final template without having to know the numerical id of the template.

```
curl -s \
  -X POST \
  --user "$MJ_APIKEY_PUBLIC:$MJ_APIKEY_PRIVATE" \
  https://api.mailjet.com/v3/send \
  -H 'Content-Type: application/json' \
  -d '{"FromEmail": "pilot@mailjet.com",
    "FromName": "Emmanuel",
    "Subject": "Receipt",
    "MJ-TemplateID": "brand3_reg_confirmation_fr",
    "MJ-TemplateLanguage": true,
        "MJ-TemplateErrorReporting":"pilot+error@mailjet.com",
        "MJ-TemplateErrorDeliver":'deliver',
    "Recipients": [
      { "Email": "passenger@mailjet.com" }
    ],
    "Vars":{
    "order": {
	    "items": [
	      {
		"title": "Brown shoes",
		"image_URL": "http://bit.ly/mj-tpl-tuto-shoes-simple",
		"price": {
		  "currency": "$",
		  "separator": ".",
		  "amount": 79.99
		},
		"size": "9.5",
		"quantity": 1
	      },
	      {
		"title": "Blue T-shirt",
		"image_URL": "http://bit.ly/mj-tpl-lang-tuto-tshirt-simple",
		"price": {
		  "currency": "$",
		  "separator": ".",
		  "amount": 29.99
		},
		"size": "S",
		"quantity": 1
	      }
	    ]
    }
  }
}'
```


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


