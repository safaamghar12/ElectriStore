import { Component , OnDestroy, signal} from '@angular/core';
import { DatePipe } from '@angular/common';


@Component({
  imports: [DatePipe],
  selector: 'app-topbar',
  styleUrl: './topbar.css',
  templateUrl: './topbar.html',
})
export class Topbar implements OnDestroy {

   currentTime = signal(new Date());

  private timer = setInterval(() => {
    this.currentTime.set(new Date());
  }, 60000);

  ngOnDestroy() {
    clearInterval(this.timer);
  }
}
