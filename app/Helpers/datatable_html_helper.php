<?php

if (!function_exists('renderToggleSwitch')) {

    function renderToggleSwitch(int $id, string $statusUrl, bool $isActive): string
    {
        $checked = $isActive ? 'checked' : '';

        return '
            <label class="relative inline-flex cursor-pointer items-center">

                <input
                    type="checkbox"
                    class="toggle-status peer sr-only"
                    value="' . $id . '"
                    ' . $checked . '
                    data-url="' . site_url($statusUrl) . '">

                <div class="peer h-6 w-11 rounded-full bg-slate-300 transition

                    dark:bg-slate-700

                    after:absolute
                    after:left-[2px]
                    after:top-[2px]
                    after:h-5
                    after:w-5
                    after:rounded-full
                    after:bg-white
                    after:transition-all

                    peer-checked:bg-green-500
                    peer-checked:after:translate-x-full">
                </div>

            </label>';
    }
}

if (!function_exists('renderEditButton')) {

    function renderEditButton(string $url): string
    {
        return '
            <a
                href="' . site_url($url) . '"
                class="rounded-lg bg-yellow-500 px-3 py-2 text-xs font-medium text-white transition hover:bg-yellow-600">

                Edit

            </a>';
    }
}

if (!function_exists('renderDeleteButton')) {

    function renderDeleteButton(string $url, string $tag = 'a'): string
    {
        if ($tag === 'button') {
            $idValue = basename($url);

            return '
                <button
                    type="button"
                    data-id="' . $idValue . '"
                    class="btn-delete rounded-lg bg-red-600 px-3 py-2 text-xs font-medium text-white transition hover:bg-red-700">

                    Delete

                </button>';
        }

        return '
            <a
                href="' . site_url($url) . '"
                class="btn-delete rounded-lg bg-red-600 px-3 py-2 text-xs font-medium text-white transition hover:bg-red-700">

                Delete

            </a>';
    }
}

if (!function_exists('renderActionButtons')) {

    function renderActionButtons(array $buttons, string $justify = ''): string
    {
        $justifyClass = $justify ? ' justify-' . $justify : '';

        return '
            <div class="flex items-center' . $justifyClass . ' gap-2">
                ' . implode('', $buttons) . '
            </div>';
    }
}
