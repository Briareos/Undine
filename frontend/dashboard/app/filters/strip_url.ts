import {Pipe, PipeTransform} from 'angular2/angular2';

@Pipe({
    name: 'stripUrl'
})
export class StripUrlPipe implements PipeTransform {
    transform(value: string, args: any[]) {
        if (value) {
            return value.replace(/^http(s)?:\/\/(www\.)?/, '').replace(/\/$/, '')
        }

        return value;
    }
}
