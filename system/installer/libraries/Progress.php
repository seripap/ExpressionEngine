<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2003 - 2010, EllisLab, Inc.
 * @license		http://expressionengine.com/user_guide/license.html
 * @link		http://expressionengine.com
 * @since		Version 2.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * ExpressionEngine Update Progress Class
 *
 * @package		ExpressionEngine
 * @subpackage	Core
 * @category	Core
 * @author		ExpressionEngine Dev Team
 * @link		http://expressionengine.com
 */
class Progress {

	var $_config = array();
	var $_prefix = '';

	/**
	 * Constructor
	 *
	 * @access	public
	 */
	function Progress()
	{
		// Empty on purpose - do not put session_start in here!
	}
	
	// --------------------------------------------------------------------

	/**
	 * Updates the current state
	 *
	 * Ideally we could use memcached or apc - but we can't, so we're stuck
	 * with a file based solution.  Using native sessions to avoid file
	 * permission problems.
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */
	function update_state($new_state)
	{
		session_start();
		$_SESSION['_progress_state'] = $new_state;
		session_write_close();
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Get State
	 *
	 * Returns the current status message
	 *
	 * @access	public
	 * @return	string
	 */
	function get_state()
	{
		session_start();
		return isset($_SESSION['_progress_state']) ? $this->_prefix.$_SESSION['_progress_state'] : FALSE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Gets the proper js and meta tag
	 *
	 * Use this on the intermediate page to make it non-js compatible
	 *
	 * @access	public
	 * @param	mixed
	 * @return	string
	 */
	function fetch_progress_header($settings)
	{
		$EE =& get_instance();
		return $EE->load->view('progress_header', $settings, TRUE);
	}
}

// END Progress class


/* End of file Progress.php */
/* Location: ./system/expressionengine/installer/libraries/Progress.php */