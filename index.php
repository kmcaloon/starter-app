<?php
// Replace Me.
?>
<style>
.lets-get-started {
    width: 100vw;
    height: 100vh;
    background: white;
    box-sizing: border-box;
    position: fixed;
    top: 0;
    left: 0;
    display: table;
    padding: 1rem;
}
.admin-bar .lets-get-started {
    top: 32px;
    height: calc(100vh - 32px);
}
.lets-get-started__inner {
    display: table-cell;
    vertical-align: middle;
    text-align: center;
    border: solid 4px black;
}
.lets-get-started__inner-text {
    font-size: 48px;
    font-family: Georgia, Times New Roman, serif;
    text-shadow: 0 7px 10px rgba(0,0,0,0.15);
    background: -webkit-linear-gradient(#585858, black);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
</style>
<section class="lets-get-started">
    <div class="lets-get-started__inner">
        <h1 class="lets-get-started__inner-text"><?php _e( 'Let\'s Get Started.', 'starter-theme' ); ?></h1>
    </div>
</section>