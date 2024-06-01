<script setup>
import axios from "@/axios";
import { reactive } from "vue";
import { login, store } from "../../store";
import { useRouter } from "vue-router";

const router = useRouter();

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

    login(response.data.return.email);
    router.push({ name: "scoops" });
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
