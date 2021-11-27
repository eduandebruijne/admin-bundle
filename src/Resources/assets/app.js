import './scss/style.scss'
import {confirmUrl, showAlert, selectMaskedForm} from './js/script';
import $ from 'jquery'
import bootstrap from 'bootstrap/dist/js/bootstrap.bundle';
import tinymce from 'tinymce'
import 'tinymce/themes/silver'

globalThis.bootstrap = bootstrap
globalThis.confirmUrl = confirmUrl
globalThis.jquery = $
globalThis.selectMaskedForm = selectMaskedForm
globalThis.showAlert = showAlert
globalThis.tinymce = tinymce