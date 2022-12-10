import { createApi, fetchBaseQuery } from "@reduxjs/toolkit/query/react";
import jwtDecode from "jwt-decode";

//Reducers
import { setCredentials, logout, session } from "../authorization/authSlice";

const baseQuery = fetchBaseQuery({
  baseUrl: "http://localhost:3001", //Production Note: Adjust baseURL to server domain.
  credentials: "include",
  prepareHeaders: (headers, { getState }) => {
    const token = getState().auth.token;
    if (token) {
      //If Access Token exists in the store, set header for verifyJWT.
      headers.set("authorization", `Bearer ${token}`);
    }
    return headers;
  },
});

const baseQueryWithReauth = async (args, api, extraOptions) => {
  let result = await baseQuery(args, api, extraOptions);

  if (result?.error?.originalStatus === 401) {
    //verifyJWT returned an error 401 on Token verification. Attempting a refresh of the Token.
    const refreshAuthResult = await baseQuery("/refreshAccessToken", api, extraOptions);

    if (refreshAuthResult?.data) {
      //A new Access Token was recieved.
      const userInfo = jwtDecode(refreshAuthResult.data.accessToken).UserInfo;
      //Store the new Access Token.
      api.dispatch(
        setCredentials({
          id: userInfo.id,
          roles: userInfo.roles,
          token: refreshAuthResult.data.accessToken,
        })
      );

      //Retry the original query with the new Access Token.
      result = await baseQuery(args, api, extraOptions);
    } else {
      //If refreshing the Access Token fails...
      result = refreshAuthResult;
      if (api.getState().auth.id) {
        api.dispatch(session({ sessionExpired: true }));
      }
      await baseQuery("/logout", api, extraOptions);
      api.dispatch(logout());
    }
  }

  return result;
};

export const authApi = createApi({
  baseQuery: baseQueryWithReauth,
  endpoints: (builder) => ({}),
});
