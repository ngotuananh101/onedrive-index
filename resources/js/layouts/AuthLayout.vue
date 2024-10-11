<template lang="">
    <div class="flex items-center justify-center h-screen max-h-screen">
        <div class="w-full md:w-[75%] bg-neutral-100 dark:bg-neutral-900 rounded-none md:rounded-2xl h-full md:h-auto p-2 md:p-5 flex flex-col md:flex-row text-neutral-900 dark:text-white">
            <router-view></router-view>
        </div>
    </div>
</template>
<script>
export default {
    name: "Auth Layout"
}
</script>
