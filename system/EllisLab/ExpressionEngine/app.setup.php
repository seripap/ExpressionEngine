<?php

use EllisLab\ExpressionEngine\Service\Config;
use EllisLab\ExpressionEngine\Service\Database;
use EllisLab\ExpressionEngine\Service\Model;
use EllisLab\ExpressionEngine\Service\Validation;
use EllisLab\ExpressionEngine\Library\Event;
use EllisLab\ExpressionEngine\Library\Filesystem;

// TODO should put the version in here at some point ...
return array(


	'vendor' => 'EllisLab',
	'product' => 'ExpressionEngine',
	'description' => 'The worlds most flexible content management system.',

	'namespace' => 'EllisLab\ExpressionEngine',

	'services' => array(

		'Event' => function($ee)
		{
			return new Event\Emitter();
		},

		'Filesystem' => function($ee)
		{
			return new Filesystem\Filesystem();
		},

		'View' => function($di, $basepath = '')
		{
			return new \EllisLab\ExpressionEngine\Service\View\ViewFactory($basepath, ee()->load, ee()->view);
		},

		'Filter' => function($di)
		{
			$filters = new \EllisLab\ExpressionEngine\Service\Filter\FilterFactory($di->make('View', '_shared/filters'));
			$filters->setDIContainer($di);
			return $filters;
		},

	),

	'services.singletons' => array(

		'Alert' => function($di)
		{
			$view = $di->make('View')->make('_shared/alert');
			return new \EllisLab\ExpressionEngine\Service\Alert\AlertCollection(ee()->session, $view);
		},

		'Config' => function($ee)
		{
			return new Config\Factory();
		},

		'Database' => function($ee)
		{
			$db_config = new Database\DBConfig(
				$ee->getConfigFile()
			);

			return new Database\Database($db_config);
		},

		'Grid' => function($di)
		{
			return new \EllisLab\ExpressionEngine\Service\Grid\Grid();
		},

		'Model' => function($ee)
		{
			$app = $ee->make('App');

			$datastore = new Model\DataStore(
				ee()->db,
				$app->getModels(),
				$ee->getPrefix()
			);

			return new Model\Frontend($datastore);
		},

		'Request' => function($ee)
		{
			return $ee->make('App')->getRequest();
		},

		'Response' => function($ee)
		{
			return $ee->make('App')->getResponse();
		},

		'Validation' => function($ee)
		{
			return new Validation\Factory();
		},
	),

	// models exposed on the model service
	'models' => array(

		# EllisLab\ExpressionEngine\Model..

			// ..\Addon
			'Extension' => 'Model\Addon\Extension',
			'Module' => 'Model\Addon\Module',
			'Plugin' => 'Model\Addon\Plugin',

			// ..\Category
			'Category' => 'Model\Category\Category',
			'CategoryGroup' => 'Model\Category\CategoryGroup',

			// ..\File
			'UploadDestination' => 'Model\File\UploadDestination',
			'FileDimension' => 'Model\File\FileDimension',
			'File' => 'Model\File\File',

			// ..\Log
			'CpLog' => 'Model\Log\CpLog',
			'DeveloperLog' => 'Model\Log\DeveloperLog',
			'EmailConsoleCache' => 'Model\Log\EmailConsoleCache',

			// ..\Security
			'Throttle' => 'Model\Security\Throttle',
			'ResetPassword' => 'Model\Security\ResetPassword',

			// ..\Session
			// empty

			// ..\Site
			'Site' => 'Model\Site\Site',
			'Stats' => 'Model\Site\Stats',

			// ..\Status
			'Status' => 'Model\Status\Status',
			'StatusGroup' => 'Model\Status\StatusGroup',

			// ..\Template
			'Template' => 'Model\Template\Template',
			'TemplateGroup'  => 'Model\Template\TemplateGroup',
			'TemplateRoute'  => 'Model\Template\TemplateRoute',
			'GlobalVariable'  => 'Model\Template\GlobalVariable',
			'Snippet' => 'Model\Template\Snippet',
			'SpecialtyTemplate' => 'Model\Template\SpecialtyTemplate',

		# EllisLab\ExpressionEngine\Module..

			// ..\Channel
			'Channel' => 'Module\Channel\Model\Channel',
			'ChannelFieldGroup'=> 'Module\Channel\Model\ChannelFieldGroup',
			'ChannelFieldStructure' => 'Module\Channel\Model\ChannelFieldStructure',
			'ChannelEntry' => 'Module\Channel\Model\ChannelEntry',
			'ChannelFormSettings' => 'Module\Channel\Model\ChannelFormSettings',

			// ..\Comment
			'Comment' => 'Module\Comment\Model\Comment',
			'CommentSubscription' => 'Module\Comment\Model\CommentSubscription',

			// ..\MailingList
			'MailingList' => 'Module\MailingList\Model\MailingList',
			'MailingListQueue' => 'Module\MailingList\Model\MailingListQueue',
			'MailingListUser' => 'Module\MailingList\Model\MailingListUser',

			// ..\Member
			'Member' => 'Module\Member\Model\Member',
			'MemberGroup' => 'Module\Member\Model\MemberGroup',

			// ..\RichTextEditor
			'RichTextEditorTool' => 'Module\RichTextEditor\Model\RichTextEditorTool',
			'RichTextEditorToolset' => 'Module\RichTextEditor\Model\RichTextEditorToolset',

			// ..\Search
			'SearchLog' => 'Module\Search\Model\SearchLog',

			// TODO: FIND A NEW HOME FOR THESE
			'EmailCache' => 'Model\EmailCache',
	)
);