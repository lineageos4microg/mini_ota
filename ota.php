<?php

#define('ARCHIVE_PATH','/srv');
#define('ARCHIVE_URL','https://download.lineage.org');
#define('PROP_TIMESTAMP_MARGIN',600);


define('ARCHIVE_PATH', getenv('ARCHIVE_PATH', true));
define('ARCHIVE_URL', getenv('ARCHIVE_URL', true));
define('PROP_TIMESTAMP_MARGIN', getenv('PROP_TIMESTAMP_MARGIN', true));


if(!isset($_SERVER['REQUEST_URI']))
	die('Invalid request / Missing $_SERVER[\'REQUEST_URI\']');

$request_uri=preg_replace('#/+#','/',$_SERVER['REQUEST_URI']);

$parts=explode('/',$request_uri);

if(count($parts) != 6)
	die('Invalid request');

$request=array(
	'apiversion'=>$parts[2],
	'device'=>$parts[3],
	'romtype'=>$parts[4],
	'incremental'=>$parts[5]
);

if($request['apiversion'] != 'v1')
	die('Unsupported API version (!= v1)');

if(!is_dir(ARCHIVE_PATH . '/' . $request['device']))
	die('Unknown device');


$incremental_time=0;

$prop_files=glob(ARCHIVE_PATH . '/' . $request['device'] . '/*.prop');
foreach($prop_files as $prop_file)
{
	if (strpos(file_get_contents($prop_file), $request['incremental']) !== false)
	{
		$incremental_time=filemtime($prop_file)+PROP_TIMESTAMP_MARGIN;
		break;
	}
}

$files=glob(ARCHIVE_PATH . '/' . $request['device'] . '/*.zip');
$roms=array();

foreach($files as $file)
{
	$filename=basename($file);
	$version=explode('-',$filename)[1];
	$rom=array(
		'datetime'=>filemtime($file),
		'filename'=>$filename,
		'id'=>strval(filemtime($file)),
		'romtype'=>$request['romtype'],
		'size'=>filesize($file),
		'url'=>ARCHIVE_URL . '/' . $request['device'] . '/' . $filename,
		'version'=>$version
	);

	if ($rom['datetime'] > $incremental_time)
		$roms[]=$rom;
}


$output['response']=$roms;

header('Content-Type: application/json');
echo json_encode($output, JSON_UNESCAPED_SLASHES);

?>
