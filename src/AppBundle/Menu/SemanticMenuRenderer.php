<?php

namespace Undine\AppBundle\Menu;

use Knp\Menu\ItemInterface;
use Knp\Menu\Renderer\ListRenderer;

/**
 * Renders MenuItem tree for semantic-ui.
 */
class SemanticMenuRenderer extends ListRenderer
{
    protected function renderList(ItemInterface $item, array $attributes, array $options)
    {
        /*
         * Return an empty string if any of the following are true:
         *   a) The menu has no children eligible to be displayed
         *   b) The depth is 0
         *   c) This menu item has been explicitly set to hide its children
         */
        if (!$item->hasChildren() || 0 === $options['depth'] || !$item->getDisplayChildren()) {
            return '';
        }

        $html = $this->format('<div'.$this->renderHtmlAttributes($attributes).'>', 'ul', $item->getLevel(), $options);
        $html .= $this->renderChildren($item, $options);
        $html .= $this->format('</div>', 'ul', $item->getLevel(), $options);

        return $html;
    }

    /**
     * Called by the parent menu item to render this menu.
     *
     * This renders the li tag to fit into the parent ul as well as its
     * own nested ul tag if this menu item has children
     *
     * @param ItemInterface $item
     * @param array         $options The options to render the item
     *
     * @return string
     */
    protected function renderItem(ItemInterface $item, array $options)
    {
        // if we don't have access or this item is marked to not be shown
        if (!$item->isDisplayed()) {
            return '';
        }

        // create an array than can be imploded as a class list
        $class = (array)$item->getAttribute('class');

        if ($this->matcher->isCurrent($item)) {
            $class[] = $options['currentClass'];
        } elseif ($this->matcher->isAncestor($item, $options['matchingDepth'])) {
            $class[] = $options['ancestorClass'];
        }

        if ($item->actsLikeFirst()) {
            $class[] = $options['firstClass'];
        }
        if ($item->actsLikeLast()) {
            $class[] = $options['lastClass'];
        }

        if ($item->hasChildren() && $options['depth'] !== 0) {
            if (null !== $options['branch_class'] && $item->getDisplayChildren()) {
                $class[] = $options['branch_class'];
            }
        } elseif (null !== $options['leaf_class']) {
            $class[] = $options['leaf_class'];
        }

        // retrieve the attributes and put the final class string back on it
        $attributes = $item->getAttributes();
        if (!empty($class)) {
            $attributes['class'] = implode(' ', $class);
        }

        $html = '';
        $isRightMenu = $item->getExtra('type', 'left-menu') === 'right-menu';

        // Don't render the container if the menu has children but is a right aligned menu
        if ($item->hasChildren() && !$isRightMenu) {
            // opening li tag
            $html = $this->format('<div'.$this->renderHtmlAttributes($attributes).'>', 'li', $item->getLevel(), $options);
        }

        // render the text/link inside the li tag
        if (!$isRightMenu) {
            $html .= $this->renderLink($item, $options);
        }

        if ($item->hasChildren()) {
            // renders the embedded ul
            $childrenClass = (array)$item->getChildrenAttribute('class');
            $childrenClass[] = 'menu_level_'.$item->getLevel();

            $childrenAttributes = $item->getChildrenAttributes();
            $childrenAttributes['class'] = implode(' ', $childrenClass);

            $html .= $this->renderList($item, $childrenAttributes, $options);

            // closing li tag
            $html .= $this->format('</div>', 'li', $item->getLevel(), $options);
        }

        return $html;
    }

    /**
     * Renders the link in a a tag with link attributes or
     * the label in a span tag with label attributes.
     *
     * Tests if item has a an uri and if not tests if it's
     * the current item and if the text has to be rendered
     * as a link or not.
     *
     * @param ItemInterface $item    The item to render the link or label for
     * @param array         $options The options to render the item
     *
     * @return string
     */
    protected function renderLink(ItemInterface $item, array $options = array())
    {
        if ($item->getUri() && (!$item->isCurrent() || $options['currentAsLink'])) {
            $text = $this->renderLinkElement($item, $options);
        } else {
            $text = $this->renderSpanElement($item, $options);
        }

        return $this->format($text, 'link', $item->getLevel(), $options);
    }

    protected function renderLinkElement(ItemInterface $item, array $options)
    {
        $attributes = $item->getLinkAttributes();
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'].' item' : 'item';

        if ($this->matcher->isCurrent($item)) {
            $attributes['class'] = $attributes['class'].' active';
        }

        $position = $item->getExtra('position', 'left');

        $link = sprintf('<a href="%s"%s>%s</a>',
            $this->escape($item->getUri()),
            $this->renderHtmlAttributes($attributes),
            $this->renderLabel($item, $options)
        );

        return $link;
    }
}
