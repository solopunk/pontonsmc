<script setup>
import {
  Disclosure,
  DisclosureButton,
  DisclosurePanel,
  Menu,
  MenuButton,
  MenuItem,
  MenuItems,
} from "@headlessui/vue";
import { Bars3Icon, XMarkIcon } from "@heroicons/vue/24/outline";
import { computed, watch, ref } from "vue";
import { useRoute, useRouter } from "vue-router";

const props = defineProps({
  loading: {
    type: Boolean,
    required: false,
  },
});

const route = useRoute();
const router = useRouter();

const loading = ref(props.loading);

// Watch for changes in the loading prop to update the local loading state
watch(
  () => props.loading,
  (newLoading) => {
    loading.value = newLoading;
  },
);

const navigation = computed(() => {
  return [
    {
      name: router.resolve({ name: "scoops" }).meta.title,
      href: router.resolve({ name: "scoops" }).href,
    },
    {
      name: router.resolve({ name: "mails" }).meta.title,
      href: router.resolve({ name: "mails" }).href,
    },
    {
      name: router.resolve({ name: "members" }).meta.title,
      href: router.resolve({ name: "members" }).href,
    },
  ];
});

const adminName = computed(() => {
  return localStorage.getItem("adminEmail").split("@")[0];
});

const picLetter = computed(() => {
  return localStorage.getItem("adminEmail").slice(0, 2).toUpperCase();
});

const pageTitle = computed(() => {
  return route.meta.title;
});

const adminNavigation = [{ name: "Déconnexion", href: "#" }];

function isLinkForCurrentPage(href) {
  return route.path === href ?? false;
}

const admin = {
  name: adminName.value,
  email: localStorage.getItem("adminEmail"),
};
</script>

<template>
  <div class="min-h-full">
    <div class="flex min-h-screen flex-col">
      <Disclosure
        as="nav"
        class="border-b border-gray-200 bg-white"
        v-slot="{ open }"
      >
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <div class="flex h-16 justify-between">
            <div class="flex">
              <div class="flex flex-shrink-0 items-center">
                <img
                  class="block h-8 w-auto lg:hidden"
                  src="https://picsum.photos/200"
                  alt="Your Company"
                />
                <img
                  class="hidden h-8 w-auto lg:block"
                  src="https://picsum.photos/200"
                  alt="Your Company"
                />
              </div>
              <div class="hidden sm:-my-px sm:ml-6 sm:flex sm:space-x-8">
                <RouterLink
                  :to="item.href"
                  v-for="item in navigation"
                  :key="item.name"
                  :class="[
                    isLinkForCurrentPage(item.href)
                      ? 'border-indigo-500 text-gray-900'
                      : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700',
                    'inline-flex items-center border-b-2 px-1 pt-1 text-sm font-medium',
                  ]"
                  :aria-current="
                    isLinkForCurrentPage(item.href) ? 'page' : undefined
                  "
                >
                  {{ item.name }}
                </RouterLink>
              </div>
            </div>
            <div class="hidden sm:ml-6 sm:flex sm:items-center">
              <!-- Profile dropdown -->
              <Menu as="div" class="relative ml-3">
                <div>
                  <MenuButton
                    class="relative flex max-w-xs items-center rounded-full bg-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                  >
                    <span class="absolute -inset-1.5" />
                    <span class="sr-only">Open admin menu</span>
                    <span
                      class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-gray-500"
                    >
                      <span class="text-sm font-medium leading-none text-white">
                        {{ picLetter }}
                      </span>
                    </span>
                  </MenuButton>
                </div>
                <transition
                  enter-active-class="transition ease-out duration-200"
                  enter-from-class="transform opacity-0 scale-95"
                  enter-to-class="transform opacity-100 scale-100"
                  leave-active-class="transition ease-in duration-75"
                  leave-from-class="transform opacity-100 scale-100"
                  leave-to-class="transform opacity-0 scale-95"
                >
                  <MenuItems
                    class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                  >
                    <MenuItem
                      v-for="item in adminNavigation"
                      :key="item.name"
                      v-slot="{ active }"
                    >
                      <a
                        :href="item.href"
                        :class="[
                          active ? 'bg-gray-100' : '',
                          'block px-4 py-2 text-sm text-gray-700',
                        ]"
                      >
                        {{ item.name }}ssss
                      </a>
                    </MenuItem>
                  </MenuItems>
                </transition>
              </Menu>
            </div>
            <div class="-mr-2 flex items-center sm:hidden">
              <!-- Mobile menu button -->
              <DisclosureButton
                class="relative inline-flex items-center justify-center rounded-md bg-white p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
              >
                <span class="absolute -inset-0.5" />
                <span class="sr-only">Open main menu</span>
                <Bars3Icon
                  v-if="!open"
                  class="block h-6 w-6"
                  aria-hidden="true"
                />
                <XMarkIcon v-else class="block h-6 w-6" aria-hidden="true" />
              </DisclosureButton>
            </div>
          </div>
        </div>

        <DisclosurePanel class="sm:hidden">
          <div class="space-y-1 pb-3 pt-2">
            <RouterLink
              :to="item.href"
              v-for="item in navigation"
              :key="item.name"
              :class="[
                isLinkForCurrentPage(item.href)
                  ? 'border-indigo-500 bg-indigo-50 text-indigo-700'
                  : 'border-transparent text-gray-600 hover:border-gray-300 hover:bg-gray-50 hover:text-gray-800',
                'block border-l-4 py-2 pl-3 pr-4 text-base font-medium',
              ]"
              :aria-current="
                isLinkForCurrentPage(item.href) ? 'page' : undefined
              "
            >
              {{ item.name }}
            </RouterLink>
          </div>
          <div class="border-t border-gray-200 pb-3 pt-4">
            <div class="flex items-center px-4">
              <div class="flex-shrink-0">
                <span
                  class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-gray-500"
                >
                  <span class="text-sm font-medium leading-none text-white">
                    {{ picLetter }}
                  </span>
                </span>
              </div>
              <div class="ml-3">
                <div class="text-base font-medium text-gray-800">
                  {{ admin.name }}
                </div>
                <div class="text-sm font-medium text-gray-500">
                  {{ admin.email }}
                </div>
              </div>
            </div>
            <div class="mt-3 space-y-1">
              <DisclosureButton
                v-for="item in adminNavigation"
                :key="item.name"
                as="a"
                :href="item.href"
                class="block px-4 py-2 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800"
              >
                {{ item.name }}
              </DisclosureButton>
            </div>
          </div>
        </DisclosurePanel>
      </Disclosure>

      <div class="relative flex-1">
        <div
          v-if="loading"
          class="absolute inset-0 flex items-center justify-center bg-white"
        >
          <div
            class="h-16 w-16 animate-spin rounded-full border-t-4 border-indigo-500"
          ></div>
        </div>
        <div v-else>
          <div class="py-10">
            <header>
              <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <h1
                  class="text-3xl font-bold leading-tight tracking-tight text-gray-900"
                >
                  {{ pageTitle }}
                </h1>
              </div>
            </header>
            <main>
              <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="px-4 py-8 sm:px-0">
                  <slot></slot>
                </div>
              </div>
            </main>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
