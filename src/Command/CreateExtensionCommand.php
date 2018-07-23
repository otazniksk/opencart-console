<?php


namespace otazniksk\OpencartConsole\Command;

use otazniksk\OpencartConsole\Helper\Helper;
use otazniksk\OpencartConsole\Helper\Templates;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

use Nette\Utils\FileSystem;

class CreateExtensionCommand extends Command
{

    const ADMIN         = 'admin';
    const CATALOG       = 'catalog';
    const VERSION       = 'v2';

    private $dir;

    /**
     * CreateExtensionCommand constructor.
     * @param string $dir
     */
    public function __construct($dir)
    {
        parent:: __construct();
        $this->dir = $dir;
    }

    /**
     * configure()
     */
    protected function configure()
    {
        $this->setName('create:extension')
             ->setDescription('Creates New Extension')
             ->setHelp('This command allows you to create new extension...');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("\n");
        $output->writeln("<comment>*******************************</comment>" . $this->dir);
        $output->writeln("<comment>**** New Extension CREATOR ****</comment>");
        $output->writeln("<comment>-------------------------------</comment>\n");

        $version                      = self::VERSION;
        $extension_file_name          = $this->choose_file_name($input, $output);
        $extension_file_name          = trim($extension_file_name);
        $extension_type               = $this->choose_type($input, $output);

        $extension_structure_admin = [];
        $extension_languages_admin = [];
        if (in_array(self::ADMIN, $extension_type)){
            $extension_structure_admin     = $this->choose_structure($input, $output, 'Admin');
            if (in_array('language', $extension_structure_admin)) {
                $extension_languages_admin = $this->choose_language($input, $output, 'Admin');
            }
        }

        $extension_structure_catalog = [];
        $extension_languages_catalog = [];
        $extension_theme_name = 'default';
        if (in_array(self::CATALOG, $extension_type)){
            $extension_structure_catalog = $this->choose_structure($input, $output, 'Catalog');
            if (in_array('language', $extension_structure_catalog)) {
                $extension_languages_catalog = $this->choose_language($input, $output, 'Catalog');
            }

            $extension_theme_name        = $this->choose_theme_name($input, $output);
        }

        $extension_destination_folder = $this->choose_destination_folder($input, $output);

        $neonStructure = Helper::getNeonStructure($this->dir);
        $mr            = $neonStructure['version'][$version]['extension'];
        $mr            = Helper::mappingReplace($mr, '{file_name}', $extension_file_name);

        // BUILD EXTENSION
        $template      = new Templates($mr);

        $xxx  = $template->createCatalogController($extension_file_name);
        FileSystem::write($this->dir . trim($extension_destination_folder) . 'xxx.php', $xxx);

        // ADMIN
        if (in_array(self::ADMIN, $extension_type)){

            $final_destination = $this->dir . '/' . trim($extension_destination_folder) . self::ADMIN . '/';

            if (in_array('controller', $extension_structure_admin)) {
                $admin_controller  = $template->createAdminController($extension_file_name);
                FileSystem::write($final_destination . $mr['admin']['controller'], $admin_controller);
            }

            if (in_array('language', $extension_structure_admin)) {
                $admin_language = $template->createAdminLanguage($extension_file_name);
                foreach ($extension_languages_admin as $language_admin) {
                    $lang_path_admin = $mr['admin']['language'];
                    $lang_path_admin = str_replace('{iso}', trim($language_admin), $lang_path_admin);
                    FileSystem::write($final_destination . $lang_path_admin, $admin_language);
                }
            }

            if (in_array('model', $extension_structure_admin)) {
                $admin_model = $template->createAdminModel($extension_file_name);
                FileSystem::write($final_destination . $mr['admin']['model'], $admin_model);
            }

            if (in_array('view', $extension_structure_admin)) {
                $admin_view = $template->createAdminView($extension_file_name);
                FileSystem::write($final_destination . $mr['admin']['view'], $admin_view);
            }
        }

        //CATALOG
        if (in_array(self::CATALOG, $extension_type)){

            $final_destination = $this->dir . '/' . trim($extension_destination_folder) . self::CATALOG . '/';

            if (in_array('controller', $extension_structure_catalog)) {
                $catalog_controller  = $template->createCatalogController($extension_file_name);
                FileSystem::write($final_destination . $mr['catalog']['controller'], $catalog_controller);
            }

            if (in_array('language', $extension_structure_catalog)) {
                $catalog_language = $template->createCatalogLanguage($extension_file_name);
                foreach ($extension_languages_catalog as $language_catalog) {
                    $lang_path_catalog = $mr['catalog']['language'];
                    $lang_path_catalog = str_replace('{iso}', trim($language_catalog), $lang_path_catalog);
                    FileSystem::write($final_destination . $lang_path_catalog, $catalog_language);
                }
            }

            if (in_array('model', $extension_structure_catalog)) {
                $catalog_model = $template->createCatalogModel($extension_file_name);
                FileSystem::write($final_destination . $mr['catalog']['model'], $catalog_model);
            }

            if (in_array('view', $extension_structure_catalog)) {
                $catalog_view = $template->createCatalogView($extension_file_name);
                $theme_path_catalog =  $mr['catalog']['view'];
                $theme_path_catalog = str_replace('{theme-name}', $extension_theme_name, $theme_path_catalog);
                FileSystem::write($final_destination . $theme_path_catalog, $catalog_view);
            }
        }

        $output->writeln("\n");
        $output->writeln("<info>Build Extension success </info>");
        $output->writeln("\n");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return array
     */
    private function choose_type(InputInterface $input, OutputInterface $output) {
        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion("\n" . 'Please select Extension type (defaults to admin):', array(self::ADMIN, self::CATALOG), self::ADMIN);
        $question->setErrorMessage('Error! type %s is invalid.');
        $question->setMultiselect(true);
        return $helper->ask($input, $output, $question);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param string $type
     * @return array
     */
    private function choose_structure(InputInterface $input, OutputInterface $output, $type = 'Admin') {
        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion("\n" . 'Please select Extension Structure for ' . $type . ' (defaults to controller, language, view):', array('controller', 'language', 'model', 'view'), 'controller, language, view');
        $question->setErrorMessage('Error! structure %s is invalid.');
        $question->setMultiselect(true);
        return $helper->ask($input, $output, $question);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param string $type
     * @return array
     */
    private function choose_language(InputInterface $input, OutputInterface $output, $type = 'Admin') {
        $languages = Helper::getNeonLanguages($this->dir);
        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion("\n" . 'Choise ' . $type . ' Languages (defaults to ' . $languages['default'] . '):', $languages['languages'], $languages['default']);
        $question->setErrorMessage('Error! language %s is invalid.');
        $question->setMultiselect(true);
        return $helper->ask($input, $output, $question);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return string
     */
    private function choose_file_name(InputInterface $input, OutputInterface $output) {
        $helper = $this->getHelper('question');
        $question = new Question("New Extension name (default is 'file_name'): ", "file_name");
        $question->setValidator(function ($answer) {
            if (!preg_match('/^[a-z0-9 _]+$/i', $answer)) {
                throw new \RuntimeException(
                    'This name is not correct!'
                );
            }
            return $answer;
        });
        return $helper->ask($input, $output, $question);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return string
     */
    private function choose_theme_name(InputInterface $input, OutputInterface $output) {
        $helper = $this->getHelper('question');
        $question = new Question("Your Catalog template name (default is 'default'): ", "default");
        return $helper->ask($input, $output, $question);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return string
     */
    private function choose_destination_folder(InputInterface $input, OutputInterface $output) {
        $helper = $this->getHelper('question');
        $question = new Question("Destination folder for build Extensions (default is /temp_extensions/ ): ", "/temp_extensions/");
        return $helper->ask($input, $output, $question);
    }
}