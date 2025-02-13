<?php

namespace Sherpa\Ui\rendering;

/**
 * Sherpa UI main class.
 */
class UI
{
    private const string REGEX_SHERPA_VAR
        = "/@Sherpa\(\.([a-z0-1\.]+)\)/i";

    protected string $type;
    protected string $title;

    protected string $layoutPath
        = __DIR__ . "/sources/layout.html";

    protected ?string $stylesheetPath
        = null;

    public function __construct(
        string $type,
        string $title,
        ?string $layoutPath = null,
        ?string $stylesheetPath = null)
    {
        $this->type = $type;
        $this->title = $title;

        if ($layoutPath !== null)
        {
            $this->layoutPath = $layoutPath;
        }

        if ($stylesheetPath !== null)
        {
            $this->stylesheetPath = $stylesheetPath;
        }
    }

    /**
     * Render default User Interface.
     */
    public function render(string $slot = ""): void
    {
        $props = $this->props();
        $this->css();
        $layout = file_get_contents($this->layoutPath);

        $layout = preg_replace_callback(
            self::REGEX_SHERPA_VAR,
            function ($result) use ($layout, $slot)
            {
                $propKey = $result[1];
                $prop = self::getPropFromKey($propKey);

                $replacement = match ($prop)
                {
                    SpecialProperty::SLOT => $slot,

                    default => $prop,
                };

                return "$replacement";
            },
            $layout);

        echo $layout;
    }

    /**
     * @return array Rendering properties
     */
    protected function props(): array
    {
        return [
            "Type" => $this->type,
            "Title" => $this->title,
            "Slot" => SpecialProperty::SLOT,
        ];
    }

    /**
     * Includes CSS stylesheet for rendering.
     */
    private function css(): void
    {
        if ($this->stylesheetPath !== null
            && !in_array($this->stylesheetPath, get_included_files()))
        {
            echo "<style>";
            include_once $this->stylesheetPath;
            echo "</style>";
        }
    }

    private function getPropFromKey(string $key): mixed
    {
        $splitKey = explode('.', $key);
        $search = $this->props();

        foreach ($splitKey as $step)
        {
            $search = $search[$step];
        }

        return $search;
    }


    /**
     * Instantiate and initialize User Interface.
     *
     * @param string $type User Interface's type
     * @param string $title Modal's title
     * @return self UI class instance
     */
    public static function make(string $type, string $title): self
    {
        return new UI($type, $title);
    }
}