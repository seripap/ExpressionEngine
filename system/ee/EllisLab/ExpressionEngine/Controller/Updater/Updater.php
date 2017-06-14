<?php
/**
 * ExpressionEngine (https://expressionengine.com)
 *
 * @link      https://expressionengine.com/
 * @copyright Copyright (c) 2003-2017, EllisLab, Inc. (https://ellislab.com)
 * @license   https://expressionengine.com/license
 */

namespace EllisLab\ExpressionEngine\Controller\Updater;

use CP_Controller;
use EllisLab\ExpressionEngine\Service;

/**
 * Updater controller, funnels update commands to the updater runner
 */
class Updater extends CP_Controller {

	/**
	 * Early permissions checks
	 */
	public function __construct()
	{
		parent::__construct();

		if (ee()->session->userdata('group_id') != 1 OR
			ee('Request')->method() != 'POST')
		{
			show_error(lang('unauthorized_access'), 403);
		}
	}

	/**
	 * Request end-point for updater tasks
	 */
	public function index()
	{
		ee()->lang->loadfile('updater');
		ee()->load->library('el_pings');
		$version_file = ee()->el_pings->get_version_info();
		$to_version = $version_file['latest_version'];

		$newer_version_available = version_compare(ee()->config->item('app_version'), $to_version, '<');
		$core_to_pro = (IS_CORE && $version_file['license_type'] == 'pro');

		if ( ! $newer_version_available && ! $core_to_pro)
		{
			return ee()->functions->redirect(ee('CP/URL', 'homepage'));
		}

		$preflight_error = NULL;
		$runner = ee('Updater/Runner');
		try
		{
			// Run preflight first and go ahead and show those errors
			$runner->runStep($runner->getFirstStep());
		}
		catch (\Exception $e)
		{
			$preflight_error = str_replace("\n", '<br>', $e->getMessage());
		}

		ee()->load->helper('text');

		$next_step = $runner->getNextStep();

		if (session_status() == PHP_SESSION_NONE) session_start();
		$_SESSION['update_step'] = $next_step;
		session_write_close();

		$vars = [
			'cp_page_title'   => lang('updating'),
			'site_name'       => ee()->config->item('site_name'),
			'current_version' => formatted_version(APP_VER),
			'to_version'      => formatted_version($to_version),
			'warn_message'    => $preflight_error,
			'first_step'      => $runner->getLanguageForStep($next_step)
		];

		ee()->javascript->set_global([
			'lang.fatal_error_caught' => lang('fatal_error_caught'),
			'lang.we_stopped_on' => lang('we_stopped_on')
		]);

		return ee('View')->make('updater/index')->render($vars);
	}

	/**
	 * AJAX endpoint for the updater
	 */
	public function run($step = NULL)
	{
		if (session_status() == PHP_SESSION_NONE) session_start();
		$step = isset($_SESSION['update_step']) ? $_SESSION['update_step'] : FALSE;

		if ($step === FALSE) return;

		$runner = ee('Updater/Runner');
		$runner->runStep($step);

		// If there is no next step, 'updateFiles' should be next in the micro app
		$_SESSION['update_step'] = $runner->getNextStep() ?: 'updateFiles';
		session_write_close();

		ee()->lang->loadfile('updater');

		return [
			'messageType' => 'success',
			'message' => $runner->getLanguageForStep($_SESSION['update_step']),
			'hasRemainingSteps' => TRUE,
			'updaterInPlace' => $_SESSION['update_step'] == 'updateFiles'
		];
	}
}
// EOF
