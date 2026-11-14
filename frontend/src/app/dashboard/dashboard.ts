import { Component } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { Sidebar } from '../shared/sidebar/sidebar';
import { Topbar } from '../shared/topbar/topbar';


@Component({
  imports: [Sidebar, Topbar, RouterOutlet],
  selector: 'app-dashboard',
  styleUrl: './dashboard.css',
  templateUrl: './dashboard.html',
})
export class Dashboard {}
