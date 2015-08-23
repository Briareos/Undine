<?php

namespace Undine\AppBundle\Menu;

use Knp\Menu\ItemInterface;
use Knp\Menu\Renderer\ListRenderer;

class SemanticBreadcrumbsRenderer extends ListRenderer
{
    protected function renderList(ItemInterface $item, array $attributes, array $options)
    {
        /**
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

    protected function renderChildren(ItemInterface $item, array $options)
    {
        // render children with a depth - 1
        if (null !== $options['depth']) {
            $options['depth'] = $options['depth'] - 1;
        }

        if (null !== $options['matchingDepth'] && $options['matchingDepth'] > 0) {
            $options['matchingDepth'] = $options['matchingDepth'] - 1;
        }

        $html = '';

        $children      = $item->getChildren();
        $childrenCount = count($children);
        $lastIndex     = $childrenCount - 1;
        $index         = 0;

        foreach ($children as $child) {
            $html .= $this->renderItem($child, $options);

            if ($index !== $lastIndex) {
                $html .= $this->renderSeparator($options);
            }
            $index++;
        }

        return $html;
    }

    protected function renderItem(ItemInterface $item, array $options)
    {
        // if we don't have access or this item is marked to not be shown
        if (!$item->isDisplayed()) {
            return '';
        }

        // create an array than can be imploded as a class list
        $class = (array) $item->getAttribute('class');

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
        if ($item->hasChildren()) {
            // opening li tag
            $html = $this->format('<div'.$this->renderHtmlAttributes($attributes).'>', 'li', $item->getLevel(), $options);
        }

        // render the text/link inside the li tag
        $html .= $this->renderLink($item, $options);

        if ($item->hasChildren()) {
            // renders the embedded ul
            $childrenClass   = (array) $item->getChildrenAttribute('class');
            $childrenClass[] = 'menu_level_'.$item->getLevel();

            $childrenAttributes          = $item->getChildrenAttributes();
            $childrenAttributes['class'] = implode(' ', $childrenClass);

            $html .= $this->renderList($item, $childrenAttributes, $options);

            // closing li tag
            $html .= $this->format('</div>', 'li', $item->getLevel(), $options);
        }

        return $html;
    }

    protected function renderLinkElement(ItemInterface $item, array $options)
    {
        $class   = (array) $item->getLinkAttribute('class');
        $class[] = 'section';

        $attributes          = $item->getLinkAttributes();
        $attributes['class'] = implode(' ', $class);

        return sprintf('<a href="%s"%s>%s</a>', $this->escape($item->getUri()), $this->renderHtmlAttributes($attributes), $this->renderLabel($item, $options));
    }

    protected function renderLink(ItemInterface $item, array $options = array())
    {
        if (!$item->actsLikeLast() && $item->getUri() && (!$item->isCurrent() || $options['currentAsLink'])) {
            $text = $this->renderLinkElement($item, $options);
        } else {
            $text = $this->renderSpanElement($item, $options);
        }

        return $this->format($text, 'link', $item->getLevel(), $options);
    }

    public function renderSeparator(array $options)
    {
        return '<i class="right angle icon divider"></i>';
    }
}