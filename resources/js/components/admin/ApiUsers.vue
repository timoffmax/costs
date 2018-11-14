<template>
    <div class="container container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card-body mt-5" v-if="$gate.allow('viewAll', 'apiUser')">
                    <passport-clients></passport-clients>
                    <passport-authorized-clients></passport-authorized-clients>
                    <passport-personal-access-tokens></passport-personal-access-tokens>
                </div>
                <div v-else>
                    <forbidden-page />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        mounted() {

        },
    }
</script>
