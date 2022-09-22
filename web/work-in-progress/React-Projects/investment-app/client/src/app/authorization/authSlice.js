import { createSlice } from "@reduxjs/toolkit";

const authSlice = createSlice({
  name: "auth",
  initialState: { id: null, roles: null, token: null, sessionExpired: false },
  reducers: {
    setCredentials: (state, action) => {
      const { id, roles, token } = action.payload;
      state.id = id;
      state.roles = roles;
      state.token = token;
    },
    logout: (state, action) => {
      state.id = null;
      state.roles = null;
      state.token = null;
    },
    session: (state, action) => {
      const { sessionExpired } = action.payload;
      state.sessionExpired = sessionExpired;
    },
  },
});

export const { setCredentials, logout, session } = authSlice.actions;

export default authSlice.reducer;

export const selectCurrentID = (state) => state.auth.id;
export const selectCurrentRoles = (state) => state.auth.roles;
export const selectCurrentToken = (state) => state.auth.token;
export const selectCurrentSessionExpired = (state) => state.auth.sessionExpired;
