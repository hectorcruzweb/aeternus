import axios from "@/axios.js"
import axiosSuper from 'axios'
const CancelToken = axiosSuper.CancelToken

export default {
    cancel: null,
    getUsuarios({
        commit
    }, url) {
        return new Promise((resolve, reject) => {
            let self = this
            axios.get(url, {
                    cancelToken: new CancelToken((c) => {
                        self.cancel = c
                    })
                })
                .then((response) => {
                    //commit('SET_USERS', response.data)
                    resolve(response)
                })
                .catch((error) => {
                    reject(error)
                })
        })
    },
    fetchUser({}, userId) {
        return new Promise((resolve, reject) => {
            axios.get(`/api/user-management/users/${userId}`)
                .then((response) => {
                    resolve(response)
                })
                .catch((error) => {
                    reject(error)
                })
        })
    },
    removeRecord({
        commit
    }, userId) {
        return new Promise((resolve, reject) => {
            axios.delete(`/api/user-management/users/${userId}`)
                .then((response) => {
                    commit('REMOVE_RECORD', userId)
                    resolve(response)
                })
                .catch((error) => {
                    reject(error)
                })
        })
    }
}
