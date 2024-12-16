/*
 * Handle file uploads - EpiWidJs framework
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

export class UploadWidget {

    constructor(element) {

        self = this;
        self.widgetElement = element;
        self.widgetElement.widgetFiles = this;

        const dropzoneparams = {
            paramName: "FileData",
            uploadMultiple: true,
            maxFilesize: 250, // MB
            queuecomplete: () => self.onUploaded()
            //success:function(file,response) {self.files['renamed'].push(response['renamed']);},
            //totaluploadprogress:function(uploadProgress,totalBytes,totalBytesSent) {console.log(uploadProgress);}
        };

        if ($.fn.dropzone) {
            self.widgetElement.classList.add('dropzone');
            $(element).dropzone(dropzoneparams);
        }
    }

    onUploaded() {
        const ajax = this.widgetElement.closest('.popup-window');

        // Reload page in popup or in window
        if (ajax)  {
            const event = new Event('epi:reload:page', {bubbles: true, cancelable: false});
            this.widgetElement.dispatchEvent(event);
        } else {
            location.reload();
        }

    }
}


window.App.widgetClasses = window.App.widgetClasses || {};
window.App.widgetClasses['upload'] = UploadWidget;
