<?php
/**
 * Import subscribers through CSV data
 * @package campaigner
 */
$fields = $_POST['import'];

// var_dump($_REQUEST);

if($_REQUEST['file']){
	// $temp_file_name = $_FILES['file']['tmp_name'];
	// $original_file_name = $_FILES['file']['name'];
    $file = $_REQUEST['file'];
	// Find file extention
	// $ext = explode ('.', $original_file_name);
	// $ext = $ext [count ($ext) - 1];
	// echo $ext;
	if(pathinfo($file, PATHINFO_EXTENSION) !== 'csv')
		return $modx->error->failure('Wrong file format');
	// Remove the extention from the original file name
	// $file_name = str_replace ($ext, '', $original_file_name);

	// Create directory if not exists
	// $modx->getService('fileHandler','modFileHandler');
	// echo $modx->campaigner->config['assetsPath'].'imports/';
	// $directory = $modx->fileHandler->make($modx->campaigner->config['assetsPath'].'imports/');
	// $directory->create();

	// $new_name = $modx->campaigner->config['assetsPath'].'imports/'.$file_name . $ext;
	// Upload to handle file
	// if(!move_uploaded_file ($temp_file_name, $new_name))
	// 	return $modx->error->failure('Could not save file');
	ini_set("auto_detect_line_endings", true);
	
	$delimiter = $_POST['delimiter'];
	if (($handle = fopen($modx->getOption('base_path') . $file, 'r')) !== FALSE)
    {
        while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
        {
            if(!$header)
                $header = $row;
            else
                $lines[] = array_combine($header, $row);
        }
        fclose($handle);
    }
    // Import subscribers
    // Test imports will stop after hitting the limit
    $i = 0;
    foreach($lines as $line) {
        if($_POST['test'] && $_POST['test'] > 0)
            if($i >= $_POST['test'])
                break;
    	$_data = array();
    	foreach($line as $key => $value) {
    		if(array_search($key, $fields) == 'groups')
    			continue;
    		$_data[array_search($key, $fields)] = $value;
    	}
    	$c = $modx->newQuery('Subscriber');
    	$c->where(array('email' => $_data['email']));
    	$subscriber = $modx->getObject('Subscriber', $c);
    	if(!$subscriber) {
    		$subscriber = $modx->newObject('Subscriber');
			// Generate subscriber key for new subscribers
    		$_data['key'] = $modx->campaigner->generate_key(array('email' => $_data['email']));
    		$_data['since'] = time();
            $_data['import'] = 1;
    	}
    	if(is_null($subscriber) and !is_object($subscriber) and !$subscriber instanceof Subscriber)
    		return $modx->error->failure('Something went wrong');
    	$subscriber->fromArray($_data);
    	$subscriber->save();
        $i++;
    }
	// if(!$_POST['save_file'])
	// 	if(!unlink($new_name))
	// 		return $modx->error->failure('Could not remove file');
	return $modx->error->success();
}
return $modx->error->failure('Please upload a file');