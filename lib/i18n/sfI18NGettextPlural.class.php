<?php
/*
 * This file is part of the sfI18NGettextPlural package.
 * (c) Edwood Ng
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this plugin.
 */

/**
 * sfI18NGettextPlural class provides a new function __plural($string, $plural, $args, $catalogue) for
 * producing plural form translation.
 *
 *
 * @package    sfI18NGettextPlural
 * @author     Edwood Ng <ed@edng.org>
 * @version    SVN: $Id$
 */
class sfI18NGettextPlural extends sfI18N
{
  /**
   * Call sfMessageFormatPlural->formatPlural to produce plural translation
   *
   * @see sfI18N
   */
  public function __plural($string, $plural, $args = array(), $catalogue = 'messages')
  {
    return $this->getMessageFormat()->formatPlural($string, $plural, $args, $catalogue);
  }

  /**
   * Returns sfMessageFormatPlural
   *
   * @see sfI18N
   */
  public function getMessageFormat()
  {
    if (!isset($this->messageFormat))
    {
      $this->messageFormat = new sfMessageFormatPlural($this->getMessageSource(), sfConfig::get('sf_charset'));

      if ($this->options['debug'])
      {
        $this->messageFormat->setUntranslatedPS(array($this->options['untranslated_prefix'], $this->options['untranslated_suffix']));
      }
    }

    return $this->messageFormat;
  }
}
