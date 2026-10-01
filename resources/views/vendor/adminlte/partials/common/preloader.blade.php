@inject('preloaderHelper', 'JeroenNoten\LaravelAdminLte\Helpers\PreloaderHelper')

@php
    $isCWrapperMode = $preloaderHelper->isPreloaderEnabled('cwrapper');
    $preloaderClasses = $preloaderHelper->makePreloaderClasses().' pos-preloader';
    $preloaderClasses .= $isCWrapperMode ? '' : ' position-fixed';
    $preloaderStyles = array_filter([
        $preloaderHelper->makePreloaderStyle(),
        $isCWrapperMode ? '' : 'z-index:9999',
        'transition:opacity .3s ease-in-out',
    ]);
@endphp

<div id="adminlte-preloader" class="{{ $preloaderClasses }}" style="{{ implode(';', $preloaderStyles) }}">
    <div class="pos-loader-mark" aria-label="Loading POS Express">
        <span class="pos-loader-cart"><i class="bi bi-cart3"></i></span>
        <span class="pos-loader-dot"></span>
        <span class="pos-loader-dot"></span>
        <span class="pos-loader-dot"></span>
    </div>
    <strong>POS Express</strong>
</div>

<script>
    (() => {
        'use strict';
        const preloader = document.getElementById('adminlte-preloader');

        if (! preloader) {
            return;
        }

        const hidePreloader = () => {
            preloader.style.opacity = '0';
            window.setTimeout(() => preloader.remove(), 300);
        };

        if (document.readyState === 'complete') {
            hidePreloader();
        } else {
            window.addEventListener('load', hidePreloader);
        }
    })();
</script>
