<?php

namespace yii2masonry;

use Yii;
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\base\Widget as Widget;

 /**
 * this widget allows you to include a pinterest like layout container to your site
 * @copyright Frenzel GmbH - www.frenzel.net
 * @link http://www.frenzel.net
 * @author Philipp Frenzel <philipp@frenzel.net>
 *
 */

class Masonry extends Widget
{

    /**
    * @var array the HTML attributes (name-value pairs) for the field container tag.
    * The values will be HTML-encoded using [[Html::encode()]].
    * If a value is null, the corresponding attribute will not be rendered.
    */
    public $options = [];


    /**
    * @var array all attributes that be accepted by the plugin, check docs!
    */
    public $clientOptions = array(
        'itemSelector' => '.item',
        'columnWidth'  => 200
    );

    private $_masonryOptionsVar;

    /**
     * Initializes the widget.
     * If you override this method, make sure you call the parent implementation first.
     */
    public function init()
    {
        ob_start();
        ob_implicit_flush(false);

        //checks for the element id
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }

        parent::init();
    }

    /**
     * Renders the widget.
     */
    public function run()
    {        
        $masonry = ob_get_clean();

        $this->_masonryOptionsVar = 'masonry_' . hash('crc32', $this->options['id']);
        $this->options['data-masonry-options'] = $this->_masonryOptionsVar;

        echo Html::beginTag('div', $this->options); //opens the container
            echo $masonry;
        echo Html::endTag('div'); //closes the container, opened on init
        $this->registerPlugin();
    }

    /**
    * Registers the widget and the related events
    */
    protected function registerPlugin()
    {
        $id = $this->options['id'];

        //get the displayed view and register the needed assets
        $view = $this->getView();
        yii2masonryAsset::register($view);
        yii2imagesloadedAsset::register($view);

        $js = array();
        
        $options = Json::encode($this->clientOptions);

        $view->registerJs("window.{$this->_masonryOptionsVar} = {$options};", View::POS_HEAD);

 //       $js[] = "var mscontainer$id = $('#$id');";
//        $js[] = "var msnry$id = mscontainer$id.masonry($options);";
//        $js[] = "msnry$id.imagesLoaded(function(){  msnry$id.masonry(); });";

        $view->registerJs("$('#$id').masonry(window[$('#$id').data('masonry-options')]);",View::POS_READY);
    }

}
