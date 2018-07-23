<?php


namespace otazniksk\OpencartConsole\Helper;

use Nette;
use Nette\Utils\FileSystem;
use Nette\Neon\Neon;

class Helper
{
    use Nette\StaticClass;

    const PATH_SETTING        = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Setting' . DIRECTORY_SEPARATOR;
    const PATH_CUSTOM_SETTING = DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR  . 'opencart' . DIRECTORY_SEPARATOR  . 'Setting' . DIRECTORY_SEPARATOR;

    /**
     * @param $opencart_dir
     * @return array
     */
    public static function getNeonStructure($opencart_dir)
    {
        // path to Setting/structure.neon
        if(file_exists($opencart_dir . self::PATH_CUSTOM_SETTING . 'structure.neon')){
            $path = $opencart_dir . self::PATH_CUSTOM_SETTING . 'structure.neon';
        }else{
            $path = self::PATH_SETTING . 'structure.neon';
        }

        $read = FileSystem::read($path);
        $data = Neon::decode($read);
        return $data;
    }

    /**
     * @param $opencart_dir
     * @return array
     */
    public static function getNeonLanguages($opencart_dir)
    {
        // path to Setting/languages.neon
        if(file_exists($opencart_dir . self::PATH_CUSTOM_SETTING . 'languages.neon')){
            $path = $opencart_dir . self::PATH_CUSTOM_SETTING . 'languages.neon';
        }else{
            $path = self::PATH_SETTING . 'languages.neon';
        }

        $read = FileSystem::read($path);
        $data = Neon::decode($read);
        return $data;
    }

    /**
     * @param array $data
     * @param string $search
     * @param string $replace
     * @return array
     */
    public static function mappingReplace($data, $search, $replace)
    {
        $mapping = [];
        foreach ($data as $key => $array) {
            array_walk($array, function (&$v) use ($search, $replace) {
                $v = str_replace($search, $replace, $v);
            });
            $mapping[$key] = $array;
        }
        return $mapping;
    }

    /**
     * @param string $name
     * @param bool $title
     * @return string
     */
    public static function normalizeName($name, $title = false)
    {
        $name_array = explode('_', $name);

        if($title){
            return implode(' ', array_map('ucfirst', $name_array));
        }else{
            return implode('', array_map('ucfirst', $name_array));
        }
    }

    /**
     * @param string $path
     * @param bool $ucfirst
     * @param null $v
     * @return string
     */
    public static function normalizePath($path, $ucfirst = true, $v = null)
    {
        $path_array = explode('/', $path);
        array_pop($path_array);
        if($ucfirst){
            return implode('', array_map('ucfirst', $path_array));
        }else{
            if($v == 'aview'){
                $path_array = array_slice($path_array, 2);
            }elseif ($v == 'cview'){
                $path_array = array_slice($path_array, 4);
            }elseif ($v == 'alanguage'){
                $path_array = array_slice($path_array, 2);
            }elseif ($v == 'clanguage'){
                $path_array = array_slice($path_array, 2);
            }elseif ($v == 'amodel'){
                $path_array = array_slice($path_array, 1);
            }elseif ($v == 'cmodel'){
                $path_array = array_slice($path_array, 1);
            }elseif ($v == 'acontroller'){
                $path_array = array_slice($path_array, 1);
            }elseif ($v == 'ccontroller'){
                $path_array = array_slice($path_array, 1);
            }
            return implode('/', $path_array) . '/';
        }
    }
}