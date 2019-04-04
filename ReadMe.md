# Recode bundle notice

 Based on [jsh11](https://github.com/jsh11/dhtmlx-bundle/blob/master/Resources/doc/index.md)   work's it's just an adaptation for symfony 4 it's work like it. 
 
 
 ## lib directory 
 
 normally it's a bundle. But for this project we needs to access request, so it isn't a standalone bundle.
 
 
 ### For modified request
 
 Go to your abstract class Abstract gantt and edit the edit function if your need.
 
  **warning array data must be edit in getResponse function for have a response**
  

### Next 

Your need to create an entity and a form for use bundle.

After you create a basecally class and a controller 

Follow the doc of the bundle [here](https://github.com/jsh11/dhtmlx-bundle/blob/master/Resources/doc/index.md)


### For use js to overide dhtmlx library

in twigs template you can use javascript methods for overide template, exemple for a view by month or days or years.


### autowiring error

If you have an error with autowiring go to folder config of you'r project, and modify the file services.yaml 
Add this line :

    Recode\DhtmlxBundle\Factory\GanttFactory:
            autowire: true
 
 

 
 
 