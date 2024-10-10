<template lang="">
    <div class="w-full md:w-[40%]">
        <div class="flex md:flex-col justify-between h-full">
            <div>
                <img
                    src="/uploads/images/logo.png"
                    alt="Logo"
                    class="w-10 mr-3"
                />
                <h1 class="text-4xl my-3">Đăng nhập</h1>
                <p class="mb-5">Tiếp tục tới OneDrive</p>
            </div>
            <!-- Switch theme -->
            <Button
                :icon="this.darkMode ? 'pi pi-sun text-xl' : 'pi pi-moon text-xl'"
                class="bg-transparent text-neutral-900 dark:text-white border-none flex-col md:flex-row justify-start"
                @click="this.toggleDarkMode"
            />
        </div>
    </div>
    <div class="w-full md:w-[60%] h-full">
        <div class="flex flex-col justify-between h-full">
            <div>
                <h1 class="text-2xl">Bước 1/3: Chuẩn bị đăng nhập</h1>
                <p class="mb-5 text-orange-400 mt-3">
                    <i class="fa-regular fa-triangle-exclamation"></i>
                    Bạn nên sử dụng REDIS để có được tốc độ ổn định cho ứng dụng
                    và hãy chắc chắn rằng bạn đã điền đầy đủ thông tin của
                    OneDrive Client ID và Client Secret trong ENV
                </p>
            </div>
            <div class="text-right">
                <Button
                    label="Đăng nhập"
                    severity="info"
                    class="rounded-xl"
                    as="a"
                    href="/auth/step-2"
                    target="_blank"
                    rel="noopener"
                />
            </div>
        </div>
    </div>
</template>
<script>
import { useSystemConfigStore } from "@/stores/systemConfigStore";
import { mapStores, mapActions, mapState } from "pinia";
export default {
    name: "Auth Step 1",
    data() {
        return {
            search: "",
        };
    },
    computed: {
        ...mapState(useSystemConfigStore, ["darkMode", "listSocial"]),
    },
    methods: {
        ...mapActions(useSystemConfigStore, ["toggleDarkMode"]),
    },
};
</script>
<style lang=""></style>
