<script setup>
import axios from "@/axios";
import { reactive } from "vue";
import { login } from "../../store";
import router from "@/router";

const credentials = reactive({
  email: "",
  password: "",
});

const sendCredentials = async () => {
  try {
    await axios.get("/sanctum/csrf-cookie");
    const response = await axios.post("api/login", {
      email: credentials.email,
      password: credentials.password,
    });

    login();
    router.push({ name: "members" });
  } catch (error) {
    console.error("Login failed", error);
  }
};
</script>

<template>
  <form @submit.prevent="sendCredentials">
    <div class="flex flex-col space-y-4">
      <input
        type="email"
        placeholder="Courriel"
        class="input input-bordered"
        v-model="credentials.email"
        required
      />
      <input
        type="password"
        placeholder="Mot de passe"
        class="input input-bordered"
        v-model="credentials.password"
        required
      />
      <button class="btn btn-primary">Connexion</button>
    </div>
  </form>
</template>
