<?php

namespace Undine\Api\Serializer;

/**
 * Mostly copied from \League\Fractal\Manager, because it contains way too much logic.
 *
 * @see \League\Fractal\Manager
 */
class Context
{
    /**
     * @var string|null
     */
    private $includes;

    /**
     * Array of scope identifiers for resources to include.
     *
     * @var array
     */
    private $requestedIncludes = [];

    /**
     * Array containing modifiers as keys and an array value of params.
     *
     * @var array
     */
    private $includeParams = [];

    /**
     * The character used to separate modifier parameters.
     *
     * @var string
     */
    private $paramDelimiter = '|';

    /**
     * Upper limit to how many levels of included data are allowed.
     *
     * @var int
     */
    private $recursionLimit = 10;

    /**
     * @param string|null $includes Ie. sites,user.sites,sites.comments:limit(5|1):order(createdAt|desc).
     */
    public function __construct($includes = null)
    {
        $includes = (string)$includes;
        $this->includes = $includes;

        if (is_string($includes)) {
            $includes = explode(',', $includes);
        }

        if (!is_array($includes)) {
            throw new \InvalidArgumentException(
                'The parseIncludes() method expects a string or an array. '.gettype($includes).' given'
            );
        }

        foreach ($includes as $include) {
            list($includeName, $allModifiersStr) = array_pad(explode(':', $include, 2), 2, null);

            // Trim it down to a cool level of recursion
            $includeName = $this->trimToAcceptableRecursionLevel($includeName);

            if (in_array($includeName, $this->requestedIncludes)) {
                continue;
            }
            $this->requestedIncludes[] = $includeName;

            // No Params? Bored
            if ($allModifiersStr === null) {
                continue;
            }

            // Matches multiple instances of 'something(foo,bar,baz)' in the string
            // I guess it ignores : so you could use anything, but probably dont do that
            preg_match_all('/([\w]+)\(([^\)]+)\)/', $allModifiersStr, $allModifiersArr);

            // [0] is full matched strings...
            $modifierCount = count($allModifiersArr[0]);

            $modifierArr = [];

            for ($modifierIt = 0; $modifierIt < $modifierCount; ++$modifierIt) {
                // [1] is the modifier
                $modifierName = $allModifiersArr[1][$modifierIt];

                // and [2] is delimited params
                $modifierParamStr = $allModifiersArr[2][$modifierIt];

                // Make modifier array key with an array of params as the value
                $modifierArr[$modifierName] = explode($this->paramDelimiter, $modifierParamStr);
            }

            $this->includeParams[$includeName] = $modifierArr;
        }

        // This should be optional and public someday, but without it includes would never show up
        $this->autoIncludeParents();

        return $this;
    }

    /**
     * @return string|null
     */
    public function getIncludesAsString()
    {
        return $this->includes;
    }

    /**
     * @return string[] A flat array of properties. Eg. 'user', 'user.sites', 'user.sites.modules'.
     */
    public function getRequestedIncludes()
    {
        return $this->requestedIncludes;
    }

    /**
     * @return array[] An array of arrays. First level is indexed by include names (see self::getRequestedIncludes).
     *                 Second level is indexed by include option (ie. 'sort', 'limit'), and its values are option
     *                 arguments (strings, some of which may be numeric).
     *
     * @see getRequestedIncludes
     */
    public function getIncludeParams()
    {
        return $this->includeParams;
    }

    /**
     * Auto-include Parents.
     *
     * Look at the requested includes and automatically include the parents if they
     * are not explicitly requested. E.g: [foo, bar.baz] becomes [foo, bar, bar.baz]
     *
     * @internal
     */
    private function autoIncludeParents()
    {
        $parsed = [];

        foreach ($this->requestedIncludes as $include) {
            $nested = explode('.', $include);

            $part = array_shift($nested);
            $parsed[] = $part;

            while (count($nested) > 0) {
                $part .= '.'.array_shift($nested);
                $parsed[] = $part;
            }
        }

        $this->requestedIncludes = array_values(array_unique($parsed));
    }

    /**
     * Trim to Acceptable Recursion Level.
     *
     * Strip off any requested resources that are too many levels deep, to avoid DiCaprio being chased
     * by trains or whatever the hell that movie was about.
     *
     * @internal
     *
     * @param string $includeName
     *
     * @return string
     */
    private function trimToAcceptableRecursionLevel($includeName)
    {
        return implode('.', array_slice(explode('.', $includeName), 0, $this->recursionLimit));
    }
}
