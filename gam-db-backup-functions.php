<?php

/**
 * Backup and download database now
 * @access public
 * @return void
 */ 
function db_backup_now()
{
	
	try
	{
		$host = DB_HOST;
		$name = DB_NAME;
		$user = DB_USER;
		$pass = DB_PASSWORD;
		$tables = '*';
			
		$path_info = wp_upload_dir();	
		$mainDir=$path_info['basedir'].'/gam-db-backup';
		if (!file_exists($mainDir))	
		      wp_mkdir_p($mainDir);		
		
		$sqlfile  =$mainDir . '/'.DB_NAME.'.sql';
		if (file_exists(sqlfile))	
		      unlink($sqlfile);
		      
		$filename=DB_NAME.'.sql';
		
		$handle = fopen($path_info['basedir'].'/gam-db-backup/'.$filename,'w+');
	 	$con = mysql_connect($host,$user,$pass);
		mysql_select_db($name,$con);
		
			//get all of the tables
			if($tables == '*')
			{
				$tables = array();
				$result = mysql_query('SHOW TABLES');
				while($row = mysql_fetch_row($result))
				{
					$tables[] = $row[0];
				}
			}
			else
			{
				$tables = is_array($tables) ? $tables : explode(',',$tables);
			}
			$return = "";
			
			//cycle through
			foreach($tables as $table)
			{
				
				$result = mysql_query('SELECT * FROM '.$table);
				$num_fields = mysql_num_fields($result);
				//$return.= 'DROP TABLE '.$table.';';
				$row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE '.$table));
				$return.= "\n\n".$row2[1].";\n\n";
				while($row = mysql_fetch_row($result))
				{
					$return.= 'INSERT INTO '.$table.' VALUES(';
					for($j=0; $j<$num_fields; $j++)
					{
						$row[$j] = mysql_real_escape_string($row[$j]);
						$return .= (isset($row[$j])) ? '"'.$row[$j].'"'	: '""'; 
						if ($j<($num_fields-1)) {
							$return .= ',';
						}
					}
					$return.= ");\n";
				}
				$return.="\n";
			}
			fwrite($handle,$return);
			fclose($handle);
			
			// Uploading File to upload directory
			$upload_path = array(
						'filename' => ($filename),
						'dir' => ($path_info['basedir'].'/gam-db-backup/'.$filename),
						'url' => ($path_info['baseurl'].'/gam-db-backup/'.$filename),
						'size' => 0
					);
			$upload_path['size']=filesize($upload_path['dir']);
			
		        $fullDownloadPath=$path_info['baseurl'].'/gam-db-backup/'.$filename;
		        
			echo json_encode(array('Success'=>true, 'FullDownloadPath'=>$fullDownloadPath));
			wp_die();
	}
	catch(Exception $e)
	{
	    $GLOBALS['errors'][] = $e;
	}		
			
			
}
	


/**
 * Save selected schedule selected option either daily, weekly or monthly.
 * It will save in database as value of gam_db_backup_schedules_options field.
 * gam_db_backup_schedules_options is common id of the all radio buttons (daily, weekly, monthly).
 * @access public
 * @return void
 */ 
function save_selected_schedule_option()
{
	
	if ( ! empty($_POST['selected_schedule_option']) )
	{
			
		update_option('gam_db_backup_schedules_options',$_POST['selected_schedule_option']);
	
		echo json_encode(array('Success'=>true, 'Message'=>__('Your schedule settings successfully saved!', 'gam-db-backup' ) ));
		
		wp_die();
	
	}
		
}
        
 /**
  * Download database based on schedules like once daily, once weekly or once monthly.
  * For Once daily   : database will store at  wp-contents/uploads/gam-db-backup/daily/
  * For Once weekly  : database will store at  wp-contents/uploads/gam-db-backup/weekly/
  * For Once monthly : database will store at  wp-contents/uploads/gam-db-backup/monthly/
  * @access public
  * @return void
  */
  function db_backup_schedules()
  {
	
	try
	{
		
		$host = DB_HOST;
		$name = DB_NAME;
		$user = DB_USER;
		$pass = DB_PASSWORD;
		$tables = '*';	
		
		$path_info = wp_upload_dir();	
		$mainDir=$path_info['basedir'].'/gam-db-backup';
		if (!file_exists($mainDir))	
		    wp_mkdir_p($mainDir);
		      
		$selected_schedule_option = get_option('gam_db_backup_schedules_options');
		
		if ( empty($selected_schedule_option) || $selected_schedule_option=='none' )
			return;
		
		
		$selected_schedule_option_dir  =$mainDir . '/'.$selected_schedule_option;
		if (!file_exists($selected_schedule_option_dir))	
		      wp_mkdir_p($selected_schedule_option_dir);	
		
		$filename=date("F j, Y, g:i a").'_'. DB_NAME .'.sql';		
				
		$handle = fopen($selected_schedule_option_dir.'/'.$filename,'w+');
		
		$con = mysql_connect($host,$user,$pass);
		mysql_select_db($name,$con);
		
		//get all of the tables
		if($tables == '*')
		{
			$tables = array();
			$result = mysql_query('SHOW TABLES');
			while($row = mysql_fetch_row($result))
			{
				$tables[] = $row[0];
			}
		}
		else
		{
			$tables = is_array($tables) ? $tables : explode(',',$tables);
		}
		$return = "";
					
		//cycle through
		foreach($tables as $table)
		{
						
			$result = mysql_query('SELECT * FROM '.$table);
			$num_fields = mysql_num_fields($result);		
			$row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE '.$table));
			$return.= "\n\n".$row2[1].";\n\n";
			while($row = mysql_fetch_row($result))
			{
				$return.= 'INSERT INTO '.$table.' VALUES(';
				for($j=0; $j<$num_fields; $j++)
				{
					$row[$j] = mysql_real_escape_string($row[$j]);
					$return .= (isset($row[$j])) ? '"'.$row[$j].'"'	: '""'; 
					if ($j<($num_fields-1)) 
					{
						$return .= ',';
				        }
			        }
			        $return.= ");\n";
		        }
			$return.="\n";
		 }
		
		fwrite($handle,$return);
		fclose($handle);
					
		// Uploading File to upload directory
		$upload_path = array(
						'filename' => ($filename),
						'dir' => ($selected_schedule_option_dir.'/'.$filename),
						'url' => ($selected_schedule_option_dir.'/'.$filename),
						'size' => 0
					);
		$upload_path['size']=filesize($upload_path['dir']);	
	}
	catch(Exception $e)
	{
	    $GLOBALS['errors'][] = $e;
	}
	
			
	
   }	



?>