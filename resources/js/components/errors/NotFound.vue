<template>
    <div class="row justify-content-center svg-container">
        <div class="col-12 error-block">
            <h1 class="text-grey text-center error-message">We're still search for your stuff</h1>
        </div>
        <div v-html="svg('404', 'svg-error')" />
    </div>
</template>

<script>
    export default {

    }
</script>

<style lang="scss" scoped>
    .svg-container {
        // Parent block height
        height: 100vh;

        // Error message styles
        .error-block {
            position: fixed;
            top: 50%;
            margin-top: -25px;
            border: 5px solid red;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.8);
            padding: 25px;
            width: 35%;

            .error-message {
                font-weight: bold;
                line-height: 2.5em;
                font-size: 2.5em;
            }
        }
    }

    // Hide all bars
    .main-header.navbar,
    footer.main-footer {
        display: none !important;
    }

    // Child block maximize
    .svg-error {
        width: 100%;
        height: 100%;
    }
</style>
