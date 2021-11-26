import $ from 'jquery'
import quill from 'quill'
import {confirmUrl, showAlert, selectMaskedForm} from './js/script';

import 'quill/dist/quill.snow.css'
import './scss/style.scss'

globalThis.confirmUrl = confirmUrl
globalThis.selectMaskedForm = selectMaskedForm
globalThis.showAlert = showAlert
globalThis.jquery = $
globalThis.Quill = quill