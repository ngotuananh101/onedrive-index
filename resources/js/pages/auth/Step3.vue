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
                <h1 class="mb-5 text-2xl">{{ $t("auth.step_3") }}</h1>
                <div class="mb-3">
                    {{ this.message }}
                </div>
                <p :class="'mb-3 ' + this.color" v-if="this.error">
                    {{ this.error }}
                </p>
                <div v-if="this.success">
                    <p class="mb-3 truncate">
                        <i
                            class="text-lg text-green-400 fa-regular fa-circle-check"
                        ></i>
                        <span class="text-lg ms-2">{{ $t("auth.access_token") }}: </span>
                        {{ this.access_token }}
                    </p>
                    <p class="mb-3 truncate">
                        <i
                            class="text-lg text-green-400 fa-regular fa-circle-check"
                        ></i>
                        <span class="text-lg ms-2">{{ $t("auth.refresh_token") }}: </span>
                        {{ this.refresh_token }}
                    </p>
                    <p class="mb-3 truncate">
                        <i
                            class="text-lg text-green-400 fa-regular fa-timer"
                        ></i>
                        <span class="text-lg ms-2">
                            {{ $t("auth.expires_in") }}:
                        </span>
                        {{ this.expires_in + " " + $t("auth.seconds") }}
                    </p>
                    <p class="mb-3 text-green-400">
                        <i
                            class="text-lg text-green-400 fa-duotone fa-solid fa-circle-info"
                        ></i>
                        {{ $t("auth.store_token_desc") }}
                    </p>
                </div>
            </div>
            <div class="text-right">
                <Button
                    class="text-white bg-sky-600 dark:hover:text-black hover:text-white"
                    :disabled="this.disable"
                    v-if="!this.success"
                    @click="makeRequest"
                >
                    {{ $t("auth.re_request") }}
                </Button>
                <Button
                    class="text-white bg-sky-600 dark:hover:text-black hover:text-white"
                    :disabled="this.disable"
                    v-if="this.success"
                    @click="returnHome"
                >
                    <i class="mr-2 fa-duotone fa-solid fa-key"></i>
                    {{ $t("auth.store_token") }}
                </Button>
            </div>
        </div>
    </div>
</template>
<script>
import { useSystemConfigStore } from "@/stores/systemConfigStore";
import { useAuthStore } from "@/stores/authStore";
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
        this.code = localStorage.getItem("code");
        if (this.code) {
            this.makeRequest();
        } else {
            this.message = this.$t("auth.error");
            this.error = this.$t("auth.error_code");
            this.color = "text-red-400";
        }
    },
    data() {
        return {
            code: "",
            disable: true,
            color: "text-orange-400",
            message: this.$t("auth.requesting"),
            error: "",
            success: false,
            access_token: "",
            refresh_token: "",
            expires_in: 0,
            interval: null,
        };
    },
    computed: {
        ...mapState(useSystemConfigStore, ["themeMode"]),
    },
    methods: {
        ...mapActions(useSystemConfigStore, ["switchThemeMode"]),
        ...mapActions(useAuthStore, ["getToken"]),
        async makeRequest() {
            this.disable = true;
            this.message = this.$t("auth.requesting");
            this.error = "";
            this.success = false;
            let data = await this.getToken(this.code);
            this.disable = false;
            if (data.status == 200) {
                this.message = this.$t("auth.success");
                this.success = true;
                this.access_token = data.access_token;
                this.refresh_token = data.refresh_token;
                this.expires_in = data.expires_in;
                if (this.interval) {
                    clearInterval(this.interval);
                }
                this.interval = setInterval(() => {
                    this.expires_in--;
                }, 1000);
            } else {
                if (data.response.data.error_description) {
                    this.message = this.$t("auth.error");
                    this.error = data.response.data.error_description;
                    this.color = "text-red-400";
                }
            }
        },
        returnHome() {
            this.$router.push({ name: "index" });
        },
    },
};
</script>
<style lang=""></style>
