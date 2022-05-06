<?php
namespace BlueChili\AjaxCart\Model;

/**
 * Interface RendererInterface
 * @package BlueChili\AjaxCart\Model
 */
interface RendererInterface
{
    /**
     * Render layout
     *
     * @param \Magento\Framework\View\Layout $layout
     * @return string
     */
    public function render($layout);
}
