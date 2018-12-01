<?php
namespace Translate\Controller;

use Sepia\PoParser\Catalog\Entry;
use Sepia\PoParser\SourceHandler\FileSystem;
use Sepia\PoParser\Parser;
use Cake\View\View;
use Sepia\PoParser\PoCompiler;

/**
 * TranslateController Controller
 *
 *
 */
class TranslateController extends AppController
{
    private $source;
    private $defaultLanguage;

    public function initialize()
    {
        parent::initialize();
        $this->defaultLanguage = env('APP_DEFAULT_LOCALE') ? env('APP_DEFAULT_LOCALE') : 'en_US';
        $this->source = ROOT.DS.APP_DIR.DS.'Locale'.DS.$this->defaultLanguage;

    }

    /**
     * Index method
     */
    public function index()
    {
        $is_enable = $this->checkExistLocale();
        if ($is_enable){
            $files = $this->myScanDir($this->source);
            $this->set('files',$files);
            $this->set('enable',true);
        }else{
            $this->set('enable',false);
        }

    }

    /**
     * Check folder locale is exists
     * @return bool
     */
    private function checkExistLocale()
    {
        if (!file_exists($this->source)){
            return false;
        }
        return true;
    }


    /**
     * Show list value
     */
    public function showMsgid()
    {
        $filePo = $this->request->getData('file');
        $file = $this->source.DS.$filePo;
        // Parse a po file
        $fileHandler = new FileSystem($file);
        $poParser = new Parser($fileHandler);
        $catalog  = $poParser->parse();
        $entries = $catalog->getEntries();
        $result = [];
        foreach ($entries as $key => $entry){
            $result[$entry->getMsgId()] = $entry->getMsgId();
        }

        $view  = new View($this->request, $this->response, null);
        $view->setTemplatePath('Translate');
        $view->layout  = 'ajax';
        $view->set('result',$result);
        $html = $view->render('Translate.msgid');

        $this->set([
            'status'     => 200,
            'message'    => __('Success'),
            'data'       => $html,
            '_serialize' => [
                'status',
                'message',
                'data',
            ],
        ]);
        return;
    }

    /**
     * Scan all file in a $fullPath
     * @param $fullPath
     * @return array
     */
    protected function myScanDir($fullPath)
    {
        $scannedFiles = scandir($fullPath);
        $files = [];
        foreach ($scannedFiles as $file) {
            if (!in_array(trim($file), ['.', '..'])) {
                $files[] = $file;
            }
        }
        return $files;
    }

    /**
     * Get value msgstr
     *
     */
    public function getMsgstr()
    {
        $file  = $this->request->getData('file');
        $msgid = $this->request->getData('msgid');
        $this->copyIfNotExists($file);
        $result = $this->findMsgstr($file, $msgid);

        $view  = new View($this->request, $this->response, null);
        $view->setTemplatePath('Translate');
        $view->layout  = 'ajax';
        $view->set('file',$file);
        $view->set('result',$result);
        $html = $view->render('Translate.msgstr');

        echo json_encode([
            'status'     => 200,
            'message'    => __d('translate','Success'),
            'data'       => $html
        ]);exit;
    }

    /**
     * Update language
     *
     */
    public function updateLanguage()
    {
        $params = $this->request->getData();
        $file   = $this->request->getData('file');
        $key    = $this->request->getData('key');

        try{
            if (!empty($params['obj'])){
                foreach ($params['obj'] as $lang => $value){
                    if (in_array($lang, $this->findFolder(ROOT . DS . APP_DIR . DS . 'Locale'))) {
                        // set path file po
                        $path = ROOT . DS . APP_DIR . DS . 'Locale';
                        // get info file
                        $fileHandler = new FileSystem($path . DS . $lang . DS . $file);
                        // parse a po file
                        $poParser = new Parser($fileHandler);
                        $catalog  = $poParser->parse();
                        // Add entry
                        $entry = new Entry($key, $value);
                        $catalog->addEntry($entry);
                        // Save changes back to a file
                        $compiler = new PoCompiler();
                        $fileHandler->save($compiler->compile($catalog));
                    } 
                }
            }

            $this->set([
                'status'     => 200,
                'message'    => __d('translate','Change file {0} success',[$file]),
                'data'       => $params,
                '_serialize' => [
                    'status',
                    'message',
                    'data',
                ],
            ]);
            return;

        }catch (\Exception $e){
            $this->set([
                'status'     => $e->getCode(),
                'message'    => __d('translate','Error {0}',[$e->getMessage()]),
                'data'       => $params,
                '_serialize' => [
                    'status',
                    'message',
                    'data',
                ],
            ]);
            return;
        }

    }


    /**
     * Copy file po form default language to folder if not exists
     * @param $file
     */
    private function copyIfNotExists($file)
    {
        $path    = ROOT . DS . APP_DIR . DS . 'Locale';
        $folders = $this->findFolder($path);
        foreach ($folders as $folder) {
            $fileExists = $this->myScanDir($path . DS . $folder);
            if ($file != null) {
                if (!in_array($file, $fileExists)) {
                    $newFile = $this->source . DS . $file;
                    copy($newFile, $path . DS . $folder . DS . $file);
                }
            }
        }
    }

    /**
     * Find all folder in Locale
     * @param $path
     * @return array
     */
    private function findFolder($path)
    {
        $result = [];
        $dir = new \DirectoryIterator($path);
        foreach ($dir as $fileInfo) {
            if ($fileInfo->isDir() && !$fileInfo->isDot()) {
                $result[] = $fileInfo->getFilename();
            }
        }
        return $result;
    }

    /**
     * get list msgstr all language in folder locale
     * @param $file
     * @param $msgid
     * @return array
     */
    private function findMsgstr($file,$msgid)
    {
        $path    = ROOT . DS . APP_DIR . DS . 'Locale';
        $folders = $this->findFolder($path);
        $result  = [];
        foreach ($folders as $folder) {
            $fileHandler     = new FileSystem($path . DS . $folder . DS . $file);
            $poParser        = new Parser($fileHandler);
            $catalog         = $poParser->parse();
            $entry           = $catalog->getEntry($msgid);
            if ($entry){
                $result[$folder] = $entry->getMsgStr();
            }else{
                $result[$folder] = $msgid;
            }

        }

        return $result;
    }
}
