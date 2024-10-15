<template>
    <div class="flex h-screen max-h-screen p-2 md:p-4">
        <div class="hidden md:block min-w-60">
            <div class="flex flex-col">
                <router-link :to="{ name: 'index' }" class="flex items-center">
                    <img
                        src="/uploads/images/logo.png"
                        alt="Logo"
                        class="w-10 mr-3"
                    />
                    <span class="text-xl font-medium dark:text-neutral-300">
                        {{ $t("app_name") }}
                    </span>
                </router-link>
            </div>
        </div>
        <div class="flex flex-col content-between grow">
            <Header />
            <div
                class="flex p-2 mt-2 grow rounded-2xl bg-neutral-100 dark:bg-neutral-900 md:p-4 md:pr-0"
                id="content-wrapper"
            >
                <div
                    class="flex flex-col max-h-full overflow-auto grow text-neutral-200"
                >
                    <router-view />
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import Header from "../components/Header.vue";
export default {
    name: "App Layout",
    components: {
        Header,
    },
    data() {
        return {
            search: "",
        };
    },
};
</script>

<style lang="scss">
#content-wrapper {
    max-height: calc(100vh - 76px);
    max-width: calc(100vw - 16px);
    overflow: auto;
}
</style>
