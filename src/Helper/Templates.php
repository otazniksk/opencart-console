<?php


namespace otazniksk\OpencartConsole\Helper;

use Nette;
use Nette\PhpGenerator;

class Templates
{
    use Nette\SmartObject;

    private $structure;

    /**
     * Templates constructor.
     * @param array $structure
     */
    public function __construct($structure = [])
    {
        $this->structure = $structure;
    }

    /**
     * @param string $name
     * @return string
     */
    public function createAdminController($name)
    {
        $start_php = "<?php\n\n";
        $class = new PhpGenerator\ClassType( Helper::normalizePath($this->structure['admin']['controller']) . Helper::normalizeName($name));
        $class->setExtends('Controller')->addComment(Helper::normalizeName($name, true) . ' admin controller class');
        $class->addProperty('error', new PhpGenerator\PhpLiteral('array()'))->setVisibility('private');

        $method = $class->addMethod('index')
                        ->setVisibility('public')
                        ->setBody('
//languages
$this->load->language(\'' . Helper::normalizePath($this->structure['admin']['language'], false, 'alanguage') . $name . '\');

//models
$this->load->model(\'' . Helper::normalizePath($this->structure['admin']['model'], false, 'amodel') . $name . '\');

//title
$this->document->setTitle($this->language->get(\'heading_title\'));

//token
$data[\'token\'] = $this->session->data[\'token\'];

if (($this->request->server[\'REQUEST_METHOD\'] == \'POST\') && $this->validate()){

    // ...
    
    $this->session->data[\'success\'] = $this->language->get(\'text_success\');
	$this->response->redirect($this->url->link(\'' . Helper::normalizePath($this->structure['admin']['controller'], false, 'acontroller') . $name . '\', \'token=\' . $this->session->data[\'token\'], true));
}

//text
$data[\'heading_title\'] = $this->language->get(\'heading_title\');
$data[\'button_save\']   = $this->language->get(\'button_save\');
$data[\'button_cancel\'] = $this->language->get(\'button_cancel\');
$data[\'text_edit\']     = $this->language->get(\'text_edit\');

//link
$data[\'action\'] = $this->url->link(\'' . Helper::normalizePath($this->structure['admin']['controller'], false, 'acontroller') . $name . '\', \'token=\' . $this->session->data[\'token\'], true);
$data[\'cancel\'] = $this->url->link(\'extension/extension\', \'token=\' . $this->session->data[\'token\'] . \'&type=module\', true);

//errors
if (isset($this->error[\'warning\'])) {
    $data[\'error_warning\'] = $this->error[\'warning\'];
} else {
    $data[\'error_warning\'] = \'\';
}

//success
if (isset($this->session->data[\'success\'])) {
    $data[\'success\'] = $this->session->data[\'success\'];
    unset($this->session->data[\'success\']);
} else {
    $data[\'success\'] = \'\';
}

//breadcrumbs
$data[\'breadcrumbs\'] = array();

$data[\'breadcrumbs\'][] = array(
    \'text\' => $this->language->get(\'text_home\'),
    \'href\' => $this->url->link(\'common/dashboard\', \'token=\' . $this->session->data[\'token\'], true)
);

$data[\'breadcrumbs\'][] = array(
    \'text\' => $this->language->get(\'text_extension\'),
    \'href\' => $this->url->link(\'extension/extension\', \'token=\' . $this->session->data[\'token\'] . \'&type=module\', true)
);

$data[\'breadcrumbs\'][] = array(
    \'text\' => $this->language->get(\'heading_title\'),
    \'href\' => $this->url->link(\'' . Helper::normalizePath($this->structure['admin']['controller'], false, 'acontroller') . $name . '\', \'token=\' . $this->session->data[\'token\'], true)
);
				
//view output
$data[\'header\']       = $this->load->controller(\'common/header\');
$data[\'column_left\']  = $this->load->controller(\'common/column_left\');
$data[\'footer\']       = $this->load->controller(\'common/footer\');

//response output
$this->response->setOutput($this->load->view(\'' . Helper::normalizePath($this->structure['admin']['view'], false, 'aview') . $name . '\', $data));
');

        $method = $class->addMethod('validate')
                        ->setVisibility('protected')
                        ->setBody('
if (!$this->user->hasPermission(\'modify\', \'' . Helper::normalizePath($this->structure['admin']['controller'], false, 'acontroller') . $name . '\')) {
    $this->error[\'warning\'] = $this->language->get(\'error_permission\');
}
		
return !$this->error;
');

        return $start_php .  $class;
    }

    /**
     * @param string $name
     * @return string
     */
    public function createAdminLanguage($name)
    {
        $start_php = "<?php\n";
        $body = '
// Heading
$_[\'heading_title\']    = \'' . Helper::normalizeName($name, true) . '\';

// Text
$_[\'text_success\']     = \'Success: You have modified this module!\';
$_[\'text_edit\']        = \'Edit\';

// Error
$_[\'error_permission\'] = \'Warning: You do not have permission to modify this module!\';
';

        return $start_php . $body;
    }

    /**
     * @param string $name
     * @return string
     */
    public function createAdminModel($name)
    {
        $start_php = "<?php\n\n";
        $class = new PhpGenerator\ClassType(Helper::normalizePath($this->structure['admin']['model']) . Helper::normalizeName($name));
        $class->setExtends('Model')->addComment(Helper::normalizeName($name, true) . ' admin model class');

        return $start_php .  $class;
    }

    /**
     * @param string $name
     * @return string
     */
    public function createAdminView($name)
    {
        $body = '<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form' . Helper::normalizeName($name) . '" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb[\'href\']; ?>"><?php echo $breadcrumb[\'text\']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } elseif ($success) {  ?>
    <div class="alert alert-success"><i class="fa fa-exclamation-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
   <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form' . Helper::normalizeName($name) . '" class="form-horizontal">
            <p>Content Here ...</p>
        </form>        
      </div>
    </div>
  </div>  
</div>';

        return $body;
    }

    /**
     * @param string $name
     * @return string
     */
    public function createCatalogController($name)
    {
        $start_php = "<?php\n\n";
        $class = new PhpGenerator\ClassType( Helper::normalizePath($this->structure['catalog']['controller']) . Helper::normalizeName($name));
        $class->setExtends('Controller')->addComment(Helper::normalizeName($name, true) . ' catalog controller class');

        $method = $class->addMethod('index')
            ->setVisibility('public')
            ->setBody('

//languages
$this->load->language(\'' . Helper::normalizePath($this->structure['catalog']['language'], false, 'clanguage') . $name . '\');

//models
$this->load->model(\'' . Helper::normalizePath($this->structure['catalog']['model'], false, 'cmodel') . $name . '\');

//heading_title
$data[\'heading_title\'] = $this->language->get(\'heading_title\');


// ...


//output view
return $this->load->view(\'' . Helper::normalizePath($this->structure['catalog']['view'], false, 'cview') . $name . '\', $data);
')
            ->addParameter('setting');

        return $start_php .  $class;
    }

    /**
     * @param string $name
     * @return string
     */
    public function createCatalogLanguage($name)
    {
        $start_php = "<?php\n";
        $body = '
// Heading
$_[\'heading_title\']    = \'' . Helper::normalizeName($name, true) . '\';
';

        return $start_php . $body;
    }

    /**
     * @param string $name
     * @return string
     */
    public function createCatalogModel($name)
    {
        $start_php = "<?php\n\n";
        $class = new PhpGenerator\ClassType(Helper::normalizePath($this->structure['catalog']['model']) . Helper::normalizeName($name));
        $class->setExtends('Model')->addComment(Helper::normalizeName($name, true) . ' catalog model class');

        return $start_php .  $class;
    }

    /**
     * @param string $name
     * @return string
     */
    public function createCatalogView($name)
    {
        $body = '<p>Content for catalog extension ' . Helper::normalizeName($name, true) . ' here ...</p>';
        return $body;
    }

}