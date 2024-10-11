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
                <h1 class="text-2xl">{{ $t("auth.step_1") }}</h1>
                <p class="mt-3 mb-5 text-red-400">
                    <i class="fa-regular fa-triangle-exclamation"></i>
                    {{ $t("auth.step_1_desc") }}
                </p>
                <Table class="mb-5">
                    <TableBody>
                        <TableRow>
                            <TableCell class="uppercase">
                                {{ $t("auth.client_id") }}
                            </TableCell>
                            <TableCell>{{ client_id }}</TableCell>
                        </TableRow>
                        <TableRow>
                            <TableCell class="uppercase">
                                {{ $t("auth.client_secret") }}
                            </TableCell>
                            <TableCell>{{ client_secret }}</TableCell>
                        </TableRow>
                        <TableRow>
                            <TableCell class="uppercase">
                                {{ $t("auth.redirect_uri") }}
                            </TableCell>
                            <TableCell>{{ redirect_uri }}</TableCell>
                        </TableRow>
                        <TableRow>
                            <TableCell class="uppercase">
                                {{ $t("auth.auth_api_url") }}
                            </TableCell>
                            <TableCell>{{ auth_api_url }}</TableCell>
                        </TableRow>
                        <TableRow>
                            <TableCell class="uppercase">
                                {{ $t("auth.drive_api_url") }}
                            </TableCell>
                            <TableCell>{{ drive_api_url }}</TableCell>
                        </TableRow>
                        <TableRow>
                            <TableCell class="uppercase">
                                {{ $t("auth.scope") }}
                            </TableCell>
                            <TableCell>{{ scope }}</TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
            <div class="text-right">
                <router-link :to="{ name: 'auth.step2' }">
                    <Button class="text-white bg-sky-600 hover:text-black">Đăng nhập</Button>
                </router-link>
            </div>
        </div>
    </div>
</template>
<script>
import { useSystemConfigStore } from "@/stores/systemConfigStore";
import { mapActions, mapState } from "pinia";
import { Button } from "@/components/ui/button";
import {
    Table,
    TableBody,
    TableCaption,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/components/ui/table";

export default {
    name: "Auth Step 1",
    components: {
        Button,
        Table,
        TableBody,
        TableCaption,
        TableCell,
        TableHead,
        TableHeader,
        TableRow,
    },
    data() {
        return {
            client_id: import.meta.env.VITE_ONE_DRIVE_CLIENT_ID,
            client_secret: "**********",
            redirect_uri: import.meta.env.VITE_ONE_DRIVE_REDIRECT_URI,
            auth_api_url: import.meta.env.VITE_ONE_DRIVE_AUTH_API_URL,
            drive_api_url: import.meta.env.VITE_ONE_DRIVE_API_URL,
            scope: import.meta.env.VITE_ONE_DRIVE_SCOPE,
        };
    },
    computed: {
        ...mapState(useSystemConfigStore, ["themeMode"]),
    },
    methods: {
        ...mapActions(useSystemConfigStore, ["switchThemeMode"]),
    },
};
</script>
<style lang=""></style>
