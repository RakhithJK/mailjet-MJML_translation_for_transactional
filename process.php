<?php 

require_once("vendor/autoload.php");
require_once("conf.php");

use \Mailjet\Resources;

$list_input = scandir($dir_input);
$list_htmls = [];
$list_htmls_final = [];


/* PART 1 : 
	recover the MJML files and process 

*/

foreach ($list_input as $file){
	if(!preg_match("/^[\._]/i",$file)){
		//generate command line 		
		$command=str_replace("[file_input]", $file , $path_mjml);
		$command=str_replace("[file_output]", str_replace(".mjml", ".html" , $file) , $command);
		$list_htmls[]=str_replace(".mjml", ".html" , $file);
		//print($command."\n");
		
		echo exec($command,$output);
		print_r($output);
	}
}
//list of html template generated pre-translation
//print_r($list_htmls);


/* PART 2 : 
	transalation of the HTML files 
*/

//find the translation files 
$list_trans = scandir($dir_trans);


foreach ($list_trans as $file){
	if(!preg_match("/^[\._]/i",$file)){
		//load translation from file		
		require_once($dir_trans.$file);
		print("TRANLATION ".$dir_trans.$file." - ".$lg."\n");
		//print_r($trans);
		foreach ($list_htmls as $html){
			$content = file_get_contents ($dir_output.$html);
			//replace all [[ ]] by translation 
			if (preg_match_all("/\[\[(.*?)\]\]/", $content, $m)) {
			      foreach ($m[1] as $i => $varname) {
				$content = str_replace($m[0][$i], sprintf('%s', $trans[$varname]), $content);
			      }
			}		
			//translated html saved as $filename_$lg.html from $filename.html  
			$file_name=str_replace(".html", "_".$lg.".html" , $html);
			$template_name=str_replace(".html","" , $file_name);	
			print("SAVE : ".$dir_output_trans.$file_name."\n");
			file_put_contents($dir_output_trans.$file_name, $content);
			$list_htmls_final[$template_name]=$file_name;	
		}
	}
}

//list of all the template to send to Mailjet
print_r($list_htmls_final);

/* PART 3 : 
	transfer by API of all template to Mailjet for use as transactional templates
*/

$mj = new \Mailjet\Client($API_KEY, $API_SECRET_KEY);

foreach ($list_htmls_final as $template_name => $file){
	print("Template ".$template_name."\n");
	$response = $mj->get(Resources::$Template, ['id' => "apikey|".$template_name]);
	if(!$response->success()){
		//template not yet created on Mailjet
		print($response->getReasonPhrase()."\n");
		print("$template_name not found on Mailjet : Let's create it !!\n");	
		$body = [
		    'Name' => $template_name,
      		    'Purposes' => [
			"transactional"
		      ]
		];
		$response = $mj->post(Resources::$Template, ['body' => $body]);
		$response->success() && var_dump($response->getData());	
	} 
	//add html content to the template
	$content = file_get_contents ($dir_output_trans.$file);
	$body = [
	    'Html-part' => $content,
	];
	//print_r($body);
	$response = $mj->post(Resources::$TemplateDetailcontent, ['id' => "apikey|".$template_name, 'body' => $body]);
	if(!$response->success()){
		//error
		print($response->getReasonPhrase()."\n");
	}	
}
