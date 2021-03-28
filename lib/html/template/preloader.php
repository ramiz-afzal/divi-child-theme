<?php if ( ! defined( 'ABSPATH' ) ) exit;?>

<style>
.loader-wrapper {
    position: fixed;
    display: flex;
    align-items: center;
    justify-content: center;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ffffff;
    z-index: 999999;
}

.loader {
    width: 5vw;
    height: 5vw;
    border: 5px solid #3c3c3c;
    border-right-color: rgba(255,255,255,0);
    border-radius: 50%;
    animation: rotate 2s linear infinite; 
}

@keyframes rotate{
    from {
        transform: rotate(0deg);
    }
    
    to {
        transform: rotate(360deg)
    }
}
</style>

<div class="loader-wrapper">
    <div class="loader"></div>
</div>

<script>
jQuery(document).ready(function($) {

    //Preloading animation
    preloaderFadeOutTime = 600;
    
    function fadePreloader() {
        var preloader = $( '.loader-wrapper' );
        preloader.fadeOut( preloaderFadeOutTime );
    }

    fadePreloader();

});
</script>