<template lang="">
    <div class="w-full md:w-[30%]">
        <div class="flex justify-between h-full md:flex-col">
            <div>
                <img
                    src="/uploads/images/logo.png"
                    alt="Logo"
                    class="w-10 mr-3"
                />
                <h1 class="my-3 text-3xl">
                    {{ $t("app_name") }}
                </h1>
                <p class="mb-5">
                    {{ $t("continue_to_drive") }}
                </p>
            </div>
            <!-- Switch theme -->
            <Button variant="ghost" size="icon" @click="switchThemeMode">
                <i
                    class="text-xl fa-solid fa-sun"
                    v-if="themeMode === 'dark'"
                ></i>
                <i
                    class="text-xl fa-solid fa-moon"
                    v-if="themeMode === 'light'"
                ></i>
            </Button>
        </div>
    </div>
    <div class="w-full md:w-[70%] h-full md:h-auto">
        <div class="flex flex-col justify-between h-full">
            <div class="grow">
                <h1 class="text-2xl">{{ $t("auth.step_2") }}</h1>
                <p class="mt-3 mb-5 text-red-400">
                    <i class="fa-regular fa-triangle-exclamation"></i>
                    {{ $t("auth.step_2_desc") }}
                </p>
                <div
                    class="relative max-w-full p-2 mb-5 border rounded-lg cursor-pointer text-balance hover:text-sky-600"
                    @click="openLoginUrl"
                >
                    <i class="absolute fa-solid fa-link right-2 top-2"></i>
                    <div class="overflow-hidden text-wrap">
                        {{ login_url }}
                    </div>
                </div>
                <p class="mb-5">
                    {{ $t("auth.step_2_desc_2") }}
                </p>
                <Input
                    type="text"
                    class="mb-5"
                    placeholder="http://localhost/?code=123456789"
                    @input="extractCode"
                />
                <p class="mb-5">
                    {{ $t("auth.step_2_desc_3") }}
                </p>
                <Input
                    disabled
                    type="text"
                    class="mb-5"
                    placeholder="123456789"
                    :value="this.code"
                />
                <p class="mb-5">
                    {{
                        this.code
                            ? $t("auth.step_2_code_valid")
                            : $t("auth.step_2_code_invalid")
                    }}
                </p>
            </div>
            <div class="text-right">
                <router-link :to="{ name: 'auth.step3' }">
                    <Button
                        class="text-white bg-sky-600 dark:hover:text-black hover:text-white"
                        :disabled="!this.code"
                    >
                        {{ $t("auth.get_token") }}
                    </Button>
                </router-link>
            </div>
        </div>
    </div>
</template>
<script>
import { useSystemConfigStore } from "@/stores/systemConfigStore";
import { mapActions, mapState } from "pinia";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";

export default {
    name: "Auth Step 1",
    components: {
        Button,
        Input,
    },
    mounted() {
        this.login_url =
            import.meta.env.VITE_ONE_DRIVE_AUTH_API_URL +
            "?client_id=" +
            import.meta.env.VITE_ONE_DRIVE_CLIENT_ID +
            "&response_type=code" +
            "&redirect_uri=" +
            import.meta.env.VITE_ONE_DRIVE_REDIRECT_URI +
            "&response_mode=query" +
            "&scope=" +
            import.meta.env.VITE_ONE_DRIVE_SCOPE.split(" ").join("+");
    },
    data() {
        return {
            login_url: "",
            code: "",
        };
    },
    computed: {
        ...mapState(useSystemConfigStore, ["themeMode"]),
    },
    methods: {
        ...mapActions(useSystemConfigStore, ["switchThemeMode"]),
        openLoginUrl: function () {
            window.open(this.login_url);
        },
        extractCode: function (e) {
            let url = new URL(e.target.value);
            this.code = url.searchParams.get("code");
            if (this.code) {
                localStorage.setItem("code", this.code);
            }
        },
    },
};
</script>
<style lang=""></style>
