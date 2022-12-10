import { authApi } from "../api/authApi";

export const authApiMutation = authApi.injectEndpoints({
  endpoints: (builder) => ({
    preLoadCheck: builder.mutation({
      query: () => ({
        url: "/preLoadCheck",
        method: "GET",
        keepUnusedDataFor: 60,
      }),
    }),
    login: builder.mutation({
      query: (credentials) => ({
        url: "/login",
        method: "POST",
        body: { ...credentials },
        keepUnusedDataFor: 60,
      }),
    }),
    forgotPassword: builder.mutation({
      query: (credentials) => ({
        url: "/forgotPassword",
        method: "POST",
        body: { ...credentials },
        keepUnusedDataFor: 60,
      }),
    }),
    changePassword: builder.mutation({
      query: (credentials) => ({
        url: "/changePassword",
        method: "POST",
        body: { ...credentials },
        keepUnusedDataFor: 60,
      }),
    }),
    userRegistration: builder.mutation({
      query: (credentials) => ({
        url: "/userRegistration",
        method: "POST",
        body: { ...credentials },
        keepUnusedDataFor: 60,
      }),
    }),
  }),
});

export const { usePreLoadCheckMutation, useLoginMutation, useForgotPasswordMutation, useChangePasswordMutation, useUserRegistrationMutation } =
  authApiMutation;
