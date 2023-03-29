<?php

header('Access-Control-Allow-Origin:*');
//if you need cookies or login etc
header('Access-Control-Allow-Credentials: true');

  header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
  header('Access-Control-Max-Age: 604800');
  //if you need special headers
  header('Access-Control-Allow-Headers: x-requested-with');

require('composer/vendor/autoload.php'); 

use Aws\S3\S3Client; 
use Aws\Exception\AwsException; 

$bucketdest="s3-bucket-staticpage";

$S3Options = 
[
	'version' => 'latest',
	'region'  => 'sa-east-1',
	'credentials' => 
	[
		'key' => 'tu-key',
		'secret' => 'tu-secret'
	]
]; 


$s3 = new S3Client($S3Options); 


// listar archivos

$archivos = $s3->listObjects(
[
	'Bucket' => $bucketdest,
	 "Prefix" => "images/"
	
]); 

$archivos = $archivos->toArray();


$fila = ""; 

foreach ($archivos['Contents'] as $archivo) 
{
	$fila .= "<tr><td>{$archivo['Key']}</td>";
	$fila .= "<td>$bucketdest</td>";
	$fila .= "<td>{$archivo['Size']}</td>";
	$fila .= "<td>{$archivo['LastModified']}</td>";
	$fila .= "<td><button onclick='getFile(&#34;{$archivo['Key']}&#34;)'>Descarga</button></td></tr>"; 
}

echo $fila; 


// carga del archivo

if(isset($_FILES['file']))
{
	$uploadObject = $s3->putObject(
		[
			'Bucket' => $bucketdest,
			'Key' => "images/".$_FILES['file']['name'],
			'SourceFile' => $_FILES['file']['tmp_name']
		]); 

	print_r($uploadObject); 
}


// descarga de archivo

if($_POST['key'])
{
	$getFile = $s3->getObject([

		'Key' => $_POST['key'],
		'Bucket' => $bucketdest
	]);

	$getFile = $getFile->toArray();

	file_put_contents($_POST['key'], $getFile['Body']); 
}





?>