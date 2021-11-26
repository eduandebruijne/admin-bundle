import $ from 'jquery'
import quill from 'quill'
import bootstrap from 'bootstrap/dist/js/bootstrap.bundle';
import {confirmUrl, showAlert, selectMaskedForm} from './js/script';

import 'quill/dist/quill.snow.css'
import './scss/style.scss'

globalThis.confirmUrl = confirmUrl
globalThis.jquery = $
globalThis.bootstrap = bootstrap
globalThis.Quill = quill
globalThis.selectMaskedForm = selectMaskedForm
globalThis.showAlert = showAlert