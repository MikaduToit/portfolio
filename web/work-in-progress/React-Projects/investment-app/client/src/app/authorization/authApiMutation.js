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
    test: builder.mutation({
      query: () => ({
        url: "/protectedRequestTest",
        method: "GET",
        keepUnusedDataFor: 60,
      }),
    }),
  }),
});

export const { usePreLoadCheckMutation, useLoginMutation, useTestMutation } = authApiMutation;
