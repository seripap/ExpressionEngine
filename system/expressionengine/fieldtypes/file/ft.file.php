<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2003 - 2011, EllisLab, Inc.
 * @license		http://expressionengine.com/user_guide/license.html
 * @link		http://expressionengine.com
 * @since		Version 2.0
 * @filesource
 */

// --------------------------------------------------------------------

/**
 * ExpressionEngine File Fieldtype Class
 *
 * @package		ExpressionEngine
 * @subpackage	Fieldtypes
 * @category	Fieldtypes
 * @author		ExpressionEngine Dev Team
 * @link		http://expressionengine.com
 */
class File_ft extends EE_Fieldtype {

	var $info = array(
		'name'		=> 'File',
		'version'	=> '1.0'
	);

	var $has_array_data = TRUE;
	
	/**
	 * Constructor
	 *
	 * @access	public
	 */
	function __construct()
	{
		parent::__construct();
		$this->EE->load->model('file_upload_preferences_model');
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Save the correct value {fieldir_\d}filename.ext
	 *
	 * @access	public
	 */
	function save($data)
	{
		if ($data != '')
		{
			$directory = $this->EE->input->post('field_id_'.$this->field_id.'_directory');

			if ( ! empty($directory))
			{
			     return '{filedir_'.$directory.'}'.$data;
			}

			return $data;
		}
	}
	
	// --------------------------------------------------------------------

	/**
	 * Validate the upload
	 *
	 * @access	public
	 */
	function validate($data)
	{
		$dir_field		= $this->field_name.'_directory';
		$hidden_field	= $this->field_name.'_hidden';
		$hidden_dir		= ($this->EE->input->post($this->field_name.'_hidden_dir')) ? $this->EE->input->post($this->field_name.'_hidden_dir') : '';
		$allowed_dirs	= array();
		
		// Default to blank - allows us to remove files
		$_POST[$this->field_name] = '';
		
		// Default directory
		$upload_directories = $this->EE->file_upload_preferences_model->get_upload_preferences($this->EE->session->userdata('group_id'));
		
		// Directory selected - switch
		$filedir = ($this->EE->input->post($dir_field)) ? $this->EE->input->post($dir_field) : '';

		foreach($upload_directories->result() as $row)
		{
			$allowed_dirs[] = $row->id;
		}		

		// Upload or maybe just a path in the hidden field?
		if (isset($_FILES[$this->field_name]) && $_FILES[$this->field_name]['size'] > 0)
		{
			$data = $this->EE->filemanager_actions('upload_file', array($filedir, $this->field_name));
			
			if (array_key_exists('error', $data))
			{
				return $data['error'];
			}
			else
			{
				$_POST[$this->field_name] = $data['name'];
			}
		}
		elseif ($this->EE->input->post($hidden_field))
		{
			$_POST[$this->field_name] = $_POST[$hidden_field];
		}
		
		$_POST[$dir_field] = $filedir;
		
		unset($_POST[$hidden_field]);
		
		// If the current file directory is not one the user has access to
		// make sure it is an edit and value hasn't changed
		
		if ($_POST[$this->field_name] && ! in_array($filedir, $allowed_dirs))
		{
			if ($filedir != '' OR ( ! $this->EE->input->post('entry_id') OR $this->EE->input->post('entry_id') == ''))
			{
				return $this->EE->lang->line('directory_no_access');
			}
			
			// The existing directory couldn't be selected because they didn't have permission to upload
			// Let's make sure that the existing file in that directory is the one that's going back in
			
			$eid = (int) $this->EE->input->post('entry_id');
			
			$this->EE->db->select($this->field_name);
			$query = $this->EE->db->get_where('channel_data', array('entry_id'=>$eid));	

			if ($query->num_rows() == 0)
			{
				return $this->EE->lang->line('directory_no_access');
			}
			
			if ('{filedir_'.$hidden_dir.'}'.$_POST[$this->field_name] != $query->row($this->field_name))
			{
				return $this->EE->lang->line('directory_no_access');
			}
			
			// Replace the empty directory with the existing directory
			$_POST[$this->field_name.'_directory'] = $hidden_dir;
		}
		
		if ($this->settings['field_required'] == 'y' && ! $_POST[$this->field_name])
		{
			return $this->EE->lang->line('required');
		}
		
		unset($_POST[$this->field_name.'_hidden_dir']);
		return array('value' => $_POST[$this->field_name]);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Show the publish field
	 *
	 * @access	public
	 */
	function display_field($data)
	{
		$vars					= array(
			'filedir'	=> '',
			'filename'	=> ''
		);
		$upload_dirs			= array();
		$allowed_file_dirs		= (isset($this->settings['allowed_directories']) && $this->settings['allowed_directories'] != 'all') ? $this->settings['allowed_directories'] : '';
		$specified_directory	= ($allowed_file_dirs == '') ? 'all' : $allowed_file_dirs;
		$content_type			= (isset($this->settings['field_content_type'])) ? $this->settings['field_content_type'] : 'all';

		// Figure out the directory and name of the file from the data 
		// (e.g. {filedir_1}filename.jpg)
		if (preg_match('/{filedir_([0-9]+)}/', $data, $matches))
		{
			$vars['filedir'] = $matches[1];
			$vars['filename'] = str_replace($matches[0], '', $data);
		}
		
		// Retrieve all directories that are both allowed for this user and
		// for this field
		$upload_directories = $this->EE->file_upload_preferences_model->get_upload_preferences(
			$this->EE->session->userdata('group_id'),
			$allowed_file_dirs
		);

		// Create the list of directories
		$upload_dirs[''] = lang('directory');
		foreach($upload_directories->result() as $row)
		{
			$upload_dirs[$row->id] = $row->name;
		}
		
		// Get the thumbnail
		$this->EE->load->library('filemanager');
		$this->EE->load->helper('html');
		$thumb_info = $this->EE->filemanager->get_thumb($vars['filename'], $vars['filedir']);
		$vars['thumb'] = img(array(
			'src' => $thumb_info['thumb'],
			'alt' => $vars['filename']
		));
		
		// Create the hidden fields for the file and directory
		$vars['hidden']	  = form_hidden($this->field_name.'_hidden', $vars['filename']);
		$vars['hidden']	 .= form_hidden($this->field_name.'_hidden_dir', $vars['filedir']);
		
		// Create a standard file upload field and dropdown for folks 
		// without javascript
		$vars['upload'] = form_upload(array(
			'name'				=> $this->field_name,
			'value'				=> $vars['filename'],
			'data-content-type'	=> $content_type,
			'data-directory'	=> $specified_directory
		));
		$vars['dropdown'] = form_dropdown($this->field_name.'_directory', $upload_dirs, $vars['filedir']);

		// Check to see if they have access to any directories to create an upload link
		$vars['upload_link'] = (count($upload_dirs) > 1) ? '<a href="#" class="choose_file" data-directory="'.$specified_directory.'">'.lang('add_file').'</a>' : lang('directory_no_access');

		// If we have a file, show the thumbnail, filename and remove link
		$vars['set_class'] = $vars['filename'] ? '' : 'js_hide';

		return $this->EE->load->ee_view('_shared/file/field', $vars, TRUE);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Prep the publish data
	 *
	 * @access	public
	 */
	function pre_process($data)
	{
		// Parse out the file info
		$file_info['path'] = '';
		
		if (preg_match('/^{filedir_(\d+)}/', $data, $matches))
		{
			// only replace it once
			$path = substr($data, 0, 10 + strlen($matches[1]));

			$file_dirs = $this->EE->functions->fetch_file_paths();
			
			if (isset($file_dirs[$matches[1]]))
			{
				$file_info['path'] = str_replace($matches[0], 
												 $file_dirs[$matches[1]], $path);
				$data = str_replace($matches[0], '', $data);				
			}
		}

		$file_info['extension'] = substr(strrchr($data, '.'), 1);
		$file_info['filename'] = basename($data, '.'.$file_info['extension']);

		return $file_info;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Replace frontend tag
	 *
	 * @access	public
	 */
	function replace_tag($file_info, $params = array(), $tagdata = FALSE)
	{
		if ($tagdata !== FALSE)
		{
			$tagdata = $this->EE->functions->prep_conditionals($tagdata, $file_info);
			$tagdata = $this->EE->functions->var_swap($tagdata, $file_info);
			
			// More an example than anything else - not particularly useful in this context
			if (isset($params['backspace']))
			{
				$tagdata = substr($tagdata, 0, - $params['backspace']);
			}
		
			return $tagdata;
		}
		else if ($file_info['path'] != '' AND $file_info['filename'] != '' AND $file_info['extension'] !== FALSE)
		{
			$full_path = $file_info['path'].$file_info['filename'].'.'.$file_info['extension'];

			if (isset($params['wrap']))
			{
				if ($params['wrap'] == 'link')
				{
					return '<a href="'.$full_path.'">'.$file_info['filename'].'</a>';
				}
				elseif ($params['wrap'] == 'image')
				{
					return '<img src="'.$full_path.'" alt="'.$file_info['filename'].'" />';
				}
			}

			return $full_path;
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Display settings screen
	 *
	 * @access	public
	 */
	function display_settings($data)
	{
		$this->EE->load->model('file_upload_preferences_model');
		
		$field_content_options = array('all' => lang('all'), 'image' => lang('type_image'));

		$this->EE->table->add_row(
			lang('field_content_file', 'field_content_file'),
			form_dropdown('file_field_content_type', $field_content_options, $data['field_content_type'], 'id="file_field_content_type"')
		);
		
		$directory_options['all'] = lang('all');
		
		$dirs = $this->EE->file_upload_preferences_model->get_upload_preferences(1);

		foreach($dirs->result_array() as $dir)
		{
			$directory_options[$dir['id']] = $dir['name'];
		}
		
		$allowed_directories = ( ! isset($data['allowed_directories'])) ? 'all' : $data['allowed_directories'];

		$this->EE->table->add_row(
			lang('allowed_dirs_file', 'allowed_dirs_file'),
			form_dropdown('file_allowed_directories', $directory_options, $allowed_directories, 'id="file_allowed_directories"')
		);		
		
	}
	
	
	
	// --------------------------------------------------------------------

	function save_settings($data)
	{		
		return array(
			'field_content_type'	=> $this->EE->input->post('file_field_content_type'),
			'allowed_directories'	=> $this->EE->input->post('file_allowed_directories'),
			'field_fmt' 			=> 'none'
		);
	}	
}

// END File_ft class

/* End of file ft.file.php */
/* Location: ./system/expressionengine/fieldtypes/ft.file.php */
