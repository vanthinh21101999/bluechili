<?php
namespace BlueChili\AjaxCart\Model;

use \BlueChili\AjaxCart\Model\Renderer\Cart;
use \BlueChili\AjaxCart\Model\Renderer\Confirmation;
use \BlueChili\AjaxCart\Model\Renderer\Related;
use \BlueChili\AjaxCart\Model\Renderer\Options;

/**
 * Block Renderer
 * @package BlueChili\AjaxCart\Model
 */
class Renderer
{
    /**#@+
     * Parts to render
     */
    const PART_CONFIRMATION = 'confirmation';
    const PART_RELATED = 'related';
    /**#@-*/

    /**
     * @var array
     */
    private $partRenderers = [
        self::PART_CONFIRMATION => Confirmation::class,
        self::PART_RELATED => Related::class
    ];

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Render layout
     *
     * @param \Magento\Framework\View\Layout $layout
     * @param string $part
     * @return string
     */
    public function render($layout, $part)
    {
        if (isset($this->partRenderers[$part])
            && $renderer = $this->objectManager->get($this->partRenderers[$part])
        ) {
            return $renderer->render($layout);
        }
        return '';
    }
}
