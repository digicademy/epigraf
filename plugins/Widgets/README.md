# Widgets plugin for Epigraf

Epigraf uses a widget system to connect HTML elements with JavaScript classes.
The widget HTML elements are generated in the backend by helper classes derived from CakePHP helpers.

Each JavaScript widget class registers a CSS class and the framework instantiates
and attaches the widget classes to the found elements.

For example, a table with the class widget-table is supplemented by a TableWidget class.
Widgets do not exclude each other, one HTML element can be attached to multiple widget instances of different classes.

The base classes of the framework are defined in htdocs/js/base.js:

- BaseModel: The BaseModel class provides lifecycle functions and methods to attach event listeners. Model class instances are not necessarily connected to the DOM. They are used to create a frontend model layer as a complement to the backend model layer. A model can have a parent and multiple children, the first constructor parameter is the parent model (which may be undefined for top level models).
- BaseWidget: All widgets derive from the BaseWidget class, which itself derives from BaseModel. A widget is defined as a model class instance attached to the DOM. The first constructor parameter is the HTML element to which the widget is attached, followed by a widget name and the parent class.
- BaseForm: The BaseForm class derives from BaseWidget and is a widget attached to form elements. It is used for form handling, preparing input to the database.
- BaseDocument: The BaseDocument class derives from BaseWidget. It is used for document handling. A document consists of several parts such as sections, footnotes and notes. Document classes hold together the different parts and manage the interaction between them.
