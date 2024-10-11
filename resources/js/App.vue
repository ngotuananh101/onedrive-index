<template>
    <router-view></router-view>
</template>
<script>
import { mapStores } from "pinia";
import { useSystemConfigStore } from "./stores/systemConfigStore";
export default {
    name: "App",
    computed: {
        ...mapStores(useSystemConfigStore),
    },
    mounted() {
        this.systemStore.init();
    },
};
</script>
