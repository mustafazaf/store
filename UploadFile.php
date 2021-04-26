<?php
namespace FME\GuestToCustomer\Block\Adminhtml\Import\Renderer;

class UploadFile extends \Magento\Backend\Block\Template
{

    // protected $_template = 'FME_GuestToCustomer::guest_import.phtml';
    protected $_coreRegistry;
    protected $feedfactory;
    public $_storeManager;
    private $_fileDriver;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Filesystem\Driver\File $fileDriver,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\File\Csv $csvProcessor,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->formKey = $formKey;
        $this->_fileDriver = $fileDriver;
        $this->_storeManager = $storeManager;
        $this->directoryList = $directoryList;
        $this->csvProcessor = $csvProcessor;
        parent::__construct($context, $data);
    }

    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $this->_element = $element;
        $html = $this->toHtml();
        return $html;
    }

    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }
    /**
     * Get MediaUrl
     * @return link
     */
    public function getMediaUrl()
    {
         return  $_mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }
   /**
    * Get MediaUrl
    * @return link
    */
    public function getMapping($id)
    {
        return $this->FieldFactory->create()->getCollection()->addOnlyMappingFilter($id);
    }
    /**
    * Chedck icon exist or not
    * @return bolean
    */
    public function getIconExist()
    {  
      if ($this->_fileDriver->isExists($this->directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA). '/urlrewritesimport/default/icon-csv.png')) {
         return true;
      }
     return false;
    }

    /**
     * Get FileCollection
     * @return json
     */
    public function getFileCollection($id)
    {
        $fileCollection = $this->getMapping($id);
        $fileColl = $fileCollection->getData();
       if ($fileColl[0]['file']=='') {
         return '';
       }
        $filePath = $this->directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA).'/urlrewritesimport/'.$fileColl[0]['file'];
        $dataComplete = $this->csvProcessor->getData($filePath);
        if (!empty($dataComplete)) {
            foreach ($dataComplete as $key => $value) {
                 $file[] = json_encode($value);
            }
            $file = json_encode($file);
        } else {
            $file = '';
        }
        return $file;
    }
}