<?php

namespace Sherpa\Ui\rendering;

/**
 * Sherpa UI main class.
 */
class UI
{
    public protected(set) string $type;
    public protected(set) string $title;
    public protected(set) string $layoutPath
        = __DIR__ . "/sources/layout.html";

    public function __construct(string $type, string $title, ?string $layoutPath = null)
    {
        $this->type = $type;
        $this->title = $title;

        if ($layoutPath !== null)
        {
            $this->layoutPath = $layoutPath;
        }
    }

    /**
     * Render default User Interface.
     */
    public function render(string $slot): void
    {
        $props = $this->props();
        $layout = file_get_contents($this->layoutPath);

        foreach (array_keys($props) as $propKey)
        {
            $prop = $props[$propKey];

            $replacement = match ($prop)
            {
                SpecialProperty::SLOT => $slot,

                default => $prop
            };

            $layout = str_replace(
                "@Sherpa(.$propKey)",
                $replacement,
                $layout);
        }

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