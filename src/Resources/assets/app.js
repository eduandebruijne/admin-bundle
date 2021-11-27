import './scss/style.scss'
import {confirmUrl, showAlert, selectMaskedForm} from './js/script';
import $ from 'jquery'
import bootstrap from 'bootstrap/dist/js/bootstrap.bundle';

/* Import TinyMCE */
import tinymce from 'tinymce';

/* Default icons are required for TinyMCE 5.3 or above */
import 'tinymce/icons/default';

/* A theme is also required */
import 'tinymce/themes/silver';

/* Import the skin */
import 'tinymce/skins/ui/oxide/skin.css';

/* Import plugins */
import 'tinymce/plugins/autolink';
import 'tinymce/plugins/lists';
import 'tinymce/plugins/link';
import 'tinymce/plugins/table';
import 'tinymce/plugins/wordcount';

globalThis.bootstrap = bootstrap
globalThis.confirmUrl = confirmUrl
globalThis.jquery = $
globalThis.selectMaskedForm = selectMaskedForm
globalThis.showAlert = showAlert
globalThis.tinymce = tinymce