<?php
class japi
{
	function __construct()
	{
		// Globalizing needed environmentdata
		global $global_path,$db;
		
		// Loading databasepointer
		$this->db = $db;
		
		// Loading AES-Data
		$this->aes_key = "sadlfzsdsaFSADf7efewszfia3g73";
		$this->aes_iv = "74692635461746291937465028371845";
		$this->aes_pointer = "";
		
		// Setting encryption vars
		$this->encrypted_data = "";
		$this->decrypted_data = "";
		
		// Setting Debug Vars
		$this->debug = "";
		$this->debug_counter = 0;
		$this->debug_logfile_path = $global_path."/intern/log/data.html";
		$this->debug_filepointer = "";
		
		// Setting output vars
		$this->output = "";
	}
	
	function debug_toggle_state()
	{
		if ($this->debug == 0)
		{
			// Enabling Debug
			$this->debug = 1;
			
			// Opening logfile to append data
			$this->debug_filepointer = fopen($this->debug_logfile_path,"a+");
			
			// Logging debug activation
			$this->debug_message("<b>Debug enabled at ".date("d.m.Y H:i")."</b>");
			
			return;
		}
		if ($this->debug == 1)
		{
			// Debug status
			$this->debug_message("<b>Debug disabled at ".date("d.m.Y H:i")."</b>");
			
			// Disabling Debug
			$this->debug = 0;
			$this->debug_counter = 0;
		
			// Closing Logfile
			fclose($this->debug_filepointer);
		}
	}
	
	function debug_message($debug_message)
	{
		if ($this->debug != 1) return;
		
		// Increase process-count
		$this->debug_counter++;
		
		// Write to log
		fwrite($this->debug_filepointer,$this->debug_counter.": ".$debug_message."<br>");
	}
	
	function crypt_open_module()
	{
		// Now we are opening the crypt-module. In this case we use AES128 aka RIJNDAEL 128
		// We use it for encryption and decryption so it should be opened only one
		$this->aes_pointer = mcrypt_module_open (MCRYPT_RIJNDAEL_256, '', MCRYPT_MODE_ECB, '');
		
		// Debug message
		$this->debug_message("Crypt module opened");
	}
	
	function crypt_init()
	{
		// We should check if there's a module avaible. if not, open it
		if ($this->aes_pointer == "") $this->crypt_open_module();
		
		// Initiating cryptographic routines
		mcrypt_generic_init ($this->aes_pointer,$this->aes_key,$this->aes_iv);
		
		// Debug message
		$this->debug_message("Crypt module initiated");
	}
	
	function crypt_deinit()
	{
		// We should check if there's a module avaible. if not, return
		if(!$this->aes_pointer) return;
	
		// Closing pointer
		mcrypt_generic_deinit($this->aes_pointer);
		
		// Debug message
		$this->debug_message("Crypt pointer closed");
	}
	
	function crypt_close_module()
	{
		// We should checking if there's a module avaible. if not, return
		if(!$this->aes_pointer) return;
	
		// Closing module + emptying pointer
		mcrypt_module_close($this->aes_pointer);
		$this->aes_pointer = "";	
		
		// Debug message
		$this->debug_message("Crypt module closed");
	}
	
	function collect_garbage()
	{
		// This is just a little garbage collector for closing pointers, etc
		if ($this->debug == 1) $this->debug_message("Collecting garbage - Good night, good fight!");
		
		// First, crypt pointer if available
		if ($this->aes_pointer != "") $this->crypt_close_module();
		
		// The encryption vars
		$this->encrypted_data = "";
		$this->decrypted_data = "";
		
		// the output var
		$this->output = "";
		
		// Next, if debug is running
		if ($this->debug == 1) $this->debug_toggle_state();
	}
	
	function encrypt_data($data)
	{
		// Now, initiate the crypt module
		$this->crypt_init();
		
		// GO - Encrypt the data
		$this->debug_message("Encrypting data");
		$this->encrypted_data = mcrypt_generic ($this->aes_pointer,$data);
		
		// DeInit
		$this->crypt_deinit();
	}
	
	function decrypt_data($data)
	{
		// this is much easier as above
		// Initiate the crypt module
		$this->crypt_init();
		
		// GO - Decrypt the data
		$this->debug_message("Decrypting data");
		$this->decrypted_data = mdecrypt_generic ($this->aes_pointer,$this->urlsafe_b64decode($data));
		
		// Unserializing data
		$this->decrypted_data = unserialize($this->decrypted_data);
		
		// DeInit
		$this->crypt_deinit();
		
	}

	function urlsafe_b64encode($string)
	{
		$data = base64_encode($string);
		$data = str_replace(array('+','/','='),array('-','_','.'),$data);
		return $data;
	}

	function urlsafe_b64decode($string)
	{
		$data = str_replace(array('-','_','.'),array('+','/','='),$string);
		$mod4 = strlen($data) % 4;
		if ($mod4)
		{
			$data .= substr('====', $mod4);
		}
		return base64_decode($data);
	}

	function verify_data()
	{
		// Check unserialized arraydata
		if (!is_numeric($this->decrypted_data['uid']))
		{
			$this->output = 0;
			$this->end_api();
			return;
		}
		if (strlen($this->decrypted_data['pwd']) != 32)
		{
			$this->output = 0;
			$this->end_api();
			return;
		}
	}

	function get_user_info()
	{
		// Try to catch some data
		$result = $this->db->query("SELECT a.id,a.user,a.race,UNIX_TIMESTAMP(a.lastaction) as date_tsp,b.name FROM
					    stu_user as a LEFT JOIN stu_allylist as b USING(allys_id) WHERE
					    a.id=".$this->decrypted_data['uid']." AND
					    pass='".str_replace("'","",$this->decrypted_data['pwd'])."' LIMIT 1",4);
		// If we fail, show it
		if ($result == 0)
		{
			$this->debug_message("Userdata lookup failed");
			$this->output = 0;
			$this->end_api();
			return;
		}
		$this->debug_message("Sending userdata");
		// Fitting data to output format
		// ID
		$this->add_output($result['id']."\n");
		// Username
		$this->add_output(stripslashes($result['user'])."\n");
		// Lastaction
		$this->add_output($result['date_tsp']."\n");
		// Allyname
		$this->add_output(stripslashes($result['name']));
	
		// Sending output
		$this->end_api();
	}

	function get_ally_info()
	{
		// Try to catch some data
		$result = $this->db->query("SELECT a.allys_id,a.name,a.praes_user_id,a.vize_user_id,a.auss_user_id,
					   COUNT(b.id) as mc FROM stu_allylist as a LEFT JOIN stu_user as b USING(allys_id)
					   WHERE a.allys_id=".$this->decrypted_data['allyid']." AND 
					   a.praes_user_id=".$this->decrypted_data['uid']."",4);
		// If we faile, show it
		if ($result == 0)
		{
			$this->debug_message("Allydata lookup failed");
			$this->output = 0;
			$this->end_api();
			return;
		}
		$this->debug_message("Sending allydata");
		// Fitting data to output format
		// ID
		$this->add_output($result['allys_id']."\n");
		// Allyname
		$this->add_output(stripslashes($result['name'])."\n");
		// Membercount
		$this->add_output($result['mc']."\n");
		// Präsident
		$array = array($result['praes_user_id'],stripslashes($this->db->query("SELECT user FROM stu_user WHERE id=".$result['praes_user_id']),1));
		$this->add_output(serialize($array)."\n");
		// Vize
		$array = array($result['vize_user_id'],stripslashes($this->db->query("SELECT user FROM stu_user WHERE id=".$result['vize_user_id']),1));
		$this->add_output(serialize($array)."\n");
		// Aussenminister
		$array = array($result['auss_user_id'],stripslashes($this->db->query("SELECT user FROM stu_user WHERE id=".$result['auss_user_id']),1));
		$this->add_output(serialize($array)."\n");
	
		// Sending output
		$this->end_api();
	}

	function end_api()
	{
		$this->output($this->output);
		$this->collect_garbage();
	}

	function output()
	{
		echo $this->output;
	}

	function add_output($string)
	{
		$this->output .= $string;
	}

	function encrypt_output($data)
	{
		// Encrypting Output
		$this->encrypt_data($data);
		$this->output = $this->encrypted_data;
	}
}
?>